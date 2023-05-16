<template>
	<q-infinite-scroll
		ref="scroll_component"
		@load="loadProducers"
		:offset="250"
		class="absolute column full-height full-width"
	>
		<!-- todo - figure how to make it 6 column -->
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
			<div class="col-12">
				123
			</div>
		</div>
		<!--				<div class="col-6 row">-->
		<!--					<q-responsive-->
		<!--						:ratio="16/9"-->
		<!--						class="col"-->
		<!--					>-->
		<!--						<q-card-->
		<!--							v-for="producer in producers"-->
		<!--							:key="producer.id"-->
		<!--							class="column home__producer-card text-white justify-center"-->
		<!--							:class="{ 'bg-light-green-2': cart.hasOwnProperty(producer.id) }"-->
		<!--							@click="show(producer.id)"-->
		<!--						>-->
		<!--							<q-img-->
		<!--								:src="producer.storefront_image ? backend_server + '/storage/' + producer.storefront_image.path : 'no-image.png'"-->
		<!--								fit="contain"-->
		<!--								class="col"-->
		<!--							/>-->
		<!--						</q-card>-->
		<!--					</q-responsive>-->
		<!--				</div>-->
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
