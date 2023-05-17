<template>
	<q-infinite-scroll
		ref="scroll_component"
		@load="loadProducers"
		:offset="250"
		class="absolute column no-wrap full-height full-width"
	>
		<div
			v-for="producer in producers"
			:key="producer.id"
			class="col-6 row items-start full-width"
			style="border:1px dashed black"
		>
			<q-responsive
				:ratio="16/9"
				class="col-12"
				style="max-height:70%; border:1px solid red"
			>
				<q-card
					flat
					class="column"
				>
					<q-img
						class="col"
						:src="producer.storefront_image ? backend_server + '/storage/' + producer.storefront_image.path : 'no-image.png'"
						fit="contain"
					/>
				</q-card>
			</q-responsive>

			<q-carousel
				v-if="producer.products.length > 0"
				v-model="slide"
				class="col-12"
				transition-prev="slide-right"
				transition-next="slide-left"
				swipeable
				animated
				control-color="white"
				infinite
				padding
				arrows
				height="30%"
			>
				<q-carousel-slide
					v-for="i in Math.ceil(producer.products.length/2)"
					:key="i"
					:name="i"
					class="q-pa-none column no-wrap"
				>
					<div class="row fit justify-start items-center q-gutter-xs q-col-gutter no-wrap">
						<q-img
							v-for="thumbnail in producer.products.map((p) => p.thumbnail).slice((i-1)*2, i*2)"
							:key="thumbnail.id"
							class="col-1"
							fit="contain"
							:src="backend_server + '/storage/' + thumbnail.path"
						/>
					</div>
				</q-carousel-slide>
			</q-carousel>

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
		const slide = ref(1)

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
			carousel,
			slide
		}
	}
})
</script>
