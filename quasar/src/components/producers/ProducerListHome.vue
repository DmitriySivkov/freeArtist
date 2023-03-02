<template>
	<q-infinite-scroll
		ref="scroll_component"
		@load="loadProducers"
	>
		<q-card
			v-for="(producer, index) in producers"
			:key="producer.id"
			class="col-12 bg-primary text-white q-mb-sm home__producer-card"
			:class="{
				'bg-light-green-2': cart.hasOwnProperty(producer.id),
				'q-mt-sm': index === 0
			}"
			@click="show(producer.id)"
		>
			<q-card-section
				horizontal
				class="row"
			>
				<div class="col-12 bg-indigo-10 q-pa-md">
					<span class="text-h6">{{ producer.display_name }}</span>
					<span
						v-if="cart.hasOwnProperty(producer.id)"
						class="text-h6"
					>
						{{ " (" + cart[producer.id].product_list.length + ")" }}
					</span>
				</div>
			</q-card-section>
			<q-card-section
				horizontal
				class="row home__producer-card_body"
			>
				<div
					style="height:200px"
					class="col-xs-12 col-md-3"
				>
					<q-img
						style="height:100%"
						:src="producer.logo ? backend_server + '/storage/' + producer.logo : 'no-image.png'"
						fit="contain"
					/>
				</div>
			</q-card-section>
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

		const cart = computed(() => cart_store.data)

		const show = (producer_id) => {
			$router.push({name:"producer_detail", params: { producer_id }})
		}

		const scroll_component = ref(null)

		const producers_length = computed(() => producers.value.length)

		const fetchProducers = async() => {
			const limit = 4

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
			scroll_component
		}
	}
})
</script>
