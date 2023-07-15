<template>
	<div class="row">
		<div class="col-xs col-lg-8">
			<q-infinite-scroll
				ref="scrollComponent"
				@load="loadProducers"
				:offset="250"
				class="column no-wrap fit"
			>
				<q-card
					v-for="producer in producers"
					:key="producer.id"
					class="col-grow row home__card"
					:class="`home__card_${isWidthThreshold ? 'expand' : 'shrink'}`"
				>
					<div class="column no-wrap fit">
						<div class="col-grow row">
							<div class="col-xs-12 col-sm">
								<q-img
									class="home__card-image fit"
									:src="producer.storefront_image ? backendServer + '/storage/' + producer.storefront_image.path : 'no-image.png'"
									fit="cover"
									:ratio="16/9"
								/>
							</div>
							<q-carousel
								v-if="producer.products.length > 0"
								v-model="slide[producer.id]"
								:vertical="isWidthThreshold"
								class="col-xs-12 col-sm-4 bg-secondary home__card-carousel"
								transition-prev="fade"
								transition-next="fade"
								swipeable
								animated
								control-color="dark"
								infinite
								:arrows="producer.products.length > 2"
							>
								<q-carousel-slide
									v-for="i in Math.ceil(producer.products.length/3)"
									:key="i"
									:name="i"
									class="q-pa-none"
								>
									<div
										v-if="!isWidthThreshold"
										class="col row justify-center"
									>
										<div class="col-xs-9">
											<div class="row justify-center q-gutter-x-xs">
												<q-img
													v-for="thumbnail in producer.products.map((p) => p.thumbnail).slice((i-1)*2, i*2)"
													:key="thumbnail.id"
													no-spinner
													class="col"
													fit="cover"
													:src="backendServer + '/storage/' + thumbnail.path"
													:ratio="4/3"
												/>
											</div>
										</div>
									</div>
									<div
										v-else
										class="column fit justify-center q-gutter-xs q-mx-none"
									>
										<q-img
											v-for="thumbnail in producer.products.map((p) => p.thumbnail).slice((i-1)*2, i*2)"
											:key="thumbnail.id"
											no-spinner
											class="col-4 q-mx-none"
											fit="cover"
											:src="backendServer + '/storage/' + thumbnail.path"
										/>
									</div>
								</q-carousel-slide>
							</q-carousel>
						</div>
						<div class="col-grow row">
							<span class="text-h6 q-pa-md">{{ producer.display_name }}</span>
						</div>
					</div>
				</q-card>
				<template v-slot:loading>
					<div class="row justify-center q-my-md">
						<q-spinner-dots
							color="primary"
							size="40px"
						/>
					</div>
				</template>
			</q-infinite-scroll>
		</div>
	</div>
</template>

<script setup>
import { computed, ref, watch, defineComponent } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { useQuasar } from "quasar"
import CheckCityDialog from "src/components/dialogs/CheckCityDialog.vue"
import { LOCATION_RANGE, LOCATION_UNKNOWN_ID } from "src/const/userLocation"

defineComponent({
	CheckCityDialog
})

const $q = useQuasar()
const cartStore = useCartStore()
const userStore = useUserStore()
const producerStore = useProducerStore()

const $router = useRouter()
const backendServer = process.env.BACKEND_SERVER

const userLocation = computed(() => userStore.location)

const userRange = computed(() => userStore.location_range)

const producers = ref([])
const slide = ref({})

const cart = computed(() => cartStore.data)

const show = (producer_id) => {
	$router.push({name:"producer_detail", params: { producer_id }})
}

const scrollComponent = ref(null)

const producersLength = computed(() => producers.value.length)

const fetchProducers = async() => {
	const limit = 5

	const response = await api.get("producers", {
		params: {
			offset: producersLength.value,
			limit,
			location: userLocation.value,
			range: userRange.value
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
	done()
}

watch(() => userRange.value,() => {
	producers.value = []
	scrollComponent.value.stop()
	scrollComponent.value.resume()
	scrollComponent.value.poll()
})

const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

</script>
