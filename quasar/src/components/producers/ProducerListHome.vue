<template>
	<q-infinite-scroll
		ref="scrollComponent"
		@load="loadProducers"
	>
		<q-page>
			<template v-if="producers.length && !isInitializing">
				<q-card
					v-for="(producer, i) in producers"
					:key="producer.id"
					class="row product__card_shrink"
				>
					<div class="column no-wrap full-width">
						<div class="col row">
							<div
								class="col-12 cursor-pointer"
								@click="$router.push({
									name: 'producer_products',
									params: { producer_id: producer.id },
									query: categories.length ? { categories: categories.join(',') } : {}
								})"
							>
								<q-img
									no-spinner
									class="product__card-image fit"
									:src="producer.logo ? `${backendServer}/storage/${producer.logo.path}` : '/no-image.png'"
									:ratio="16/9"
								/>
							</div>
							<q-carousel
								:ref="el => carousel.push(el)"
								v-model="slide[producer.id]"
								class="col-12 bg-secondary product__card-carousel q-px-xs"
								transition-prev="slide-right"
								transition-next="slide-left"
								swipeable
								animated
								:draggable="false"
								keep-alive
							>
								<q-carousel-slide
									v-for="i in Math.ceil(producer.products.length/2)"
									:key="i"
									:name="i"
									class="q-pa-none overflow-hidden"
								>
									<div
										class="col row"
										:class="
											producer.products.length > 2 ?
												(
													i === 1 ? 'justify-start'
													: (i === Math.ceil(producer.products.length/2) ? 'justify-end' : 'justify-center')
												)
												: 'justify-center'
										"
									>
										<div class="col-10">
											<div
												class="row q-gutter-xs q-py-xs no-wrap"
												:class="
													producer.products.length > 2 ?
														(
															i === 1 ? 'justify-start'
															: (i === Math.ceil(producer.products.length/2) ? 'justify-end' : 'justify-center')
														)
														: 'justify-center'
												"
											>
												<q-card
													v-for="product in producer.products.slice((i-1)*3 - (i === 1 ? 0 : i), i*3 - (i === 1 ? 0 : i-1))"
													:key="product.thumbnail.id"
													class="col-6 full-height"
												>
													<q-img
														no-spinner
														:src="backendServer + '/storage/' + product.thumbnail.path"
													/>
												</q-card>
												<div
													v-if="
														producer.products.length > 2 &&
															producer.products.length % 2 !== 0 &&
															i === Math.ceil(producer.products.length/2)
													"
													class="col-6"
												></div>
											</div>
										</div>
									</div>
								</q-carousel-slide>

								<template #control>
									<q-carousel-control
										v-if="slide[producer.id] !== 1"
										position="left"
										:offset="[0, 0]"
										class="flex flex-center"
									>
										<q-btn
											push
											round
											dense
											color="orange"
											text-color="black"
											icon="arrow_left"
											@click="carousel[i].previous()"
										/>
									</q-carousel-control>

									<q-carousel-control
										v-if="producer.products.length > 2 && slide[producer.id] !== Math.ceil(producer.products.length/2)"
										position="right"
										:offset="[0, 0]"
										class="flex flex-center"
									>
										<q-btn
											push
											round
											dense
											color="orange"
											text-color="black"
											icon="arrow_right"
											@click="carousel[i].next()"
										/>
									</q-carousel-control>
								</template>
							</q-carousel>
						</div>
						<q-separator />
						<div class="col-grow row">
							<span class="text-h6 q-pa-md">{{ producer.display_name }}</span>
						</div>
					</div>
				</q-card>
			</template>
			<template v-else-if="!producers.length && !isInitializing">
				<!-- todo - nothing found -->
			</template>
			<template v-else>
				<ProducerListHomeSkeleton />
			</template>
		</q-page>
	</q-infinite-scroll>
</template>

<script setup>
import { computed, ref, watch, onBeforeUnmount } from "vue"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useQuasar } from "quasar"
import { debounce } from "lodash"
import ProducerListHomeSkeleton from "src/components/skeletons/ProducerListHomeSkeleton.vue"

const props = defineProps({
	categories: Array
})

const $q = useQuasar()
const cartStore = useCartStore()
const userStore = useUserStore()

const backendServer = process.env.BACKEND_SERVER

const userLocation = computed(() => userStore.location)

const userRange = computed(() => userStore.location_range)

const producers = ref([])
const slide = ref({})
const carousel = ref([])

const cart = computed(() => cartStore.data)

const scrollComponent = ref(null)

onBeforeUnmount(() => {
	scrollComponent.value.stop()
})

const fetchProducers = async() => {
	const limit = 5

	if (!producers.value.length) {
		isInitializing.value = true
	}

	const response = await api.get("producers", {
		params: {
			offset: !isInitializing.value ?
				producers.value.length : 0,
			location: userLocation.value,
			range: userRange.value,
			categories: props.categories
		}
	})

	producers.value = [...producers.value, ...response.data]

	slide.value = {
		...slide.value,
		...response.data.reduce(
			(carry, i) => ({...carry, [i.id]: 1}), {}
		)
	}

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}

const loadProducers = async (index, done) => {
	await fetchProducers()

	if (isInitializing.value) {
		isInitializing.value = false
	}

	done()
}

const isInitializing = ref(false)

const reinit = debounce(() => {
	scrollComponent.value.stop()
	scrollComponent.value.resume()
	scrollComponent.value.poll()
}, 700)

watch([
	() => userRange.value,
	() => userLocation.value.id,
	() => props.categories
],() => {
	producers.value = []
	isInitializing.value = true
	reinit()
}
, {
	deep: true
})
</script>
