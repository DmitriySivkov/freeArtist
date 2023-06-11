<template>
	<q-infinite-scroll
		ref="scroll_component"
		@load="loadProducers"
		:offset="250"
		class="column no-wrap full-height full-width"
	>
		<q-card
			v-for="producer in producers"
			:key="producer.id"
			class="col-grow row home__card"
			:class="`home__card_${isWidthThreshold ? 'expand' : 'shrink'}`"
		>
			<div class="column no-wrap fit">
				<div class="col-grow row">
					<q-img
						class="col-xs-12 col-sm home__card-image"
						:src="producer.storefront_image ? backend_server + '/storage/' + producer.storefront_image.path : 'no-image.png'"
						fit="cover"
						:ratio="16/9"
					/>
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
								class="column no-wrap"
							>
								<div class="col-4 row justify-center">
									<div class="col-xs-9">
										<div class="row justify-center q-col-gutter-x-xs no-wrap">
											<q-img
												v-for="thumbnail in producer.products.map((p) => p.thumbnail).slice((i-1)*2, i*2)"
												:key="thumbnail.id"
												no-spinner
												class="col-6"
												fit="contain"
												:src="backend_server + '/storage/' + thumbnail.path"
											/>
										</div>
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
									:src="backend_server + '/storage/' + thumbnail.path"
								/>
							</div>
						</q-carousel-slide>
					</q-carousel>
				</div>
				<div class="col-2 row">

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
</template>

<script>
import { computed, ref, watch } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { useCartStore } from "src/stores/cart"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { useQuasar } from "quasar"
export default ({
	setup() {
		const $q = useQuasar()
		const cart_store = useCartStore()
		const user_store = useUserStore()
		const producer_store = useProducerStore()

		const $router = useRouter()
		const user = computed(() => user_store.$state)
		const backend_server = process.env.BACKEND_SERVER

		const user_city = ref(user.value.location ?
			user.value.location.city.name_ru : null
		)

		const producers = ref([])
		const slide = ref({})

		const cart = computed(() => cart_store.data)

		const show = (producer_id) => {
			$router.push({name:"producer_detail", params: { producer_id }})
		}

		const scroll_component = ref(null)

		const producers_length = computed(() => producers.value.length)

		const fetchProducers = async() => {
			const limit = 5

			// todo - wtf is fetching state
			producer_store.setFetchingState(true)

			const response = await api.get("producers", {
				params: {
					offset: producers_length.value,
					limit,
					location_range: user.value.location_range,
					city: user_city.value,
				}
			})

			producers.value = [...producers.value, ...response.data]

			slide.value = {
				...slide.value,
				...response.data.reduce(
					(carry, i) => ({...carry, [i.id]: 1}), {}
				)
			}

			producer_store.setFetchingState(false)

			if (response.data.length < limit && scroll_component.value) {
				scroll_component.value.stop()
			}
		}

		const loadProducers = async (index, done) => {
			await fetchProducers()
			done()
		}

		watch(() => user.value.location_range,() => {
			producers.value = []
			scroll_component.value.stop()
			scroll_component.value.resume()
			scroll_component.value.poll()
		})

		const isWidthThreshold = computed(() => $q.screen.width >= $q.screen.sizes.sm)

		return {
			producers,
			cart,
			show,
			backend_server,
			loadProducers,
			scroll_component,
			slide,
			isWidthThreshold
		}
	}
})
</script>
