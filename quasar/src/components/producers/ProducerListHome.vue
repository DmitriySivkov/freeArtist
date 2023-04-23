<template>
	<q-infinite-scroll
		ref="scroll_component"
		class="column no-wrap absolute full-height full-width q-mt-xs q-col-gutter-xs"
		@load="loadProducers"
		:offset="250"
	>
		<div
			v-for="producer in producers"
			:key="producer.id"
			class="col-6 home__producer-card q-pl-none"
		>
			<div class="row full-height">
				<q-card
					class="col-12 bg-primary text-white"
					:class="{ 'bg-light-green-2': cart.hasOwnProperty(producer.id) }"
					@click="show(producer.id)"
				>
					<q-card-section class="row full-height q-pa-none">
						<div class="col-3 column">
							<div class="col-12">
								<q-img
									:src="producer.logo ? backend_server + '/storage/' + producer.logo : 'no-image.png'"
									height="100%"
									fit="cover"
									class="rounded-borders"
								/>
							</div>
						</div>
						<div class="col-9 column q-pl-xs">
							<div class="col-auto text-body1">
								<span>{{ producer.display_name }}</span>
								<span v-if="cart.hasOwnProperty(producer.id)">
									{{ " (" + cart[producer.id].product_list.length + ")" }}
								</span>
							</div>
							<div class="col-grow">
								<q-carousel
									v-if="producer.products.length > 0"
									class="full-height rounded-borders"
									swipeable
									animated
									v-model="carousel"
									thumbnails
									infinite
								>
									<q-carousel-slide
										v-for="product in producer.products"
										:key="product.id"
										:name="carousel"
										:img-src="backend_server + '/storage/' + product.thumbnail.path"
									/>
								</q-carousel>
							</div>
						</div>
					</q-card-section>
				</q-card>
			</div>
		</div>
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
export default ({
	setup() {
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
		const carousel = ref({})

		const cart = computed(() => cart_store.data)

		const show = (producer_id) => {
			$router.push({name:"producer_detail", params: { producer_id }})
		}

		const scroll_component = ref(null)

		const producers_length = computed(() => producers.value.length)

		const fetchProducers = async() => {
			const limit = 5

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

		return {
			producers,
			cart,
			show,
			backend_server,
			loadProducers,
			scroll_component,
			carousel
		}
	}
})
</script>
