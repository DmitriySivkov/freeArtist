<template>
	<q-infinite-scroll
		ref="scrollComponent"
		@load="load"
		:offset="250"
		class="column no-wrap fit q-mt-md"
	>
		<template v-if="products.length && !isInitializing">
			<q-card
				v-for="product in products"
				:key="product.id"
				class="col-grow row q-mb-md"
			>
				<div class="column no-wrap fit">
					<div class="col-grow row">
						<div class="col cursor-pointer">
							<q-img
								no-spinner
								class="fit"
								:src="product.thumbnail ? `${backendServer}/storage/${product.thumbnail.path}` : '/no-image.png'"
								fit="cover"
								:ratio="16/9"
							/>
						</div>
					</div>
					<div class="col row">
						<span class="text-h6 q-pa-md">{{ product.title }}</span>
					</div>
				</div>
			</q-card>
		</template>
		<template v-else>
		<!--			<ProducerListHomeSkeleton />-->
		</template>
	</q-infinite-scroll>
</template>

<script setup>
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { computed, ref } from "vue"
import { useCart } from "src/composables/cart"
import { useCartStore } from "src/stores/cart"
import { useProducerStore } from "src/stores/producer"

const $router = useRouter()
const cart_store = useCartStore()
const producerStore = useProducerStore()
const cart = computed(() => cart_store.data)
const backendServer = process.env.BACKEND_SERVER

const products = ref([])
const scrollComponent = ref(null)
const isInitializing = ref(true)

const load = async (index, done) => {
	await fetchProducts()

	isInitializing.value = false

	done()
}

const fetchProducts = async() => {
	const limit = 5

	const response = await api.get(`producers/${$router.currentRoute.value.params.producer_id}/products`, {
		params: {
			offset: products.value.length
		}
	})

	products.value = [...products.value, ...response.data]

	if (response.data.length < limit && scrollComponent.value) {
		scrollComponent.value.stop()
	}
}


// const { products, increase, decrease, orderAmountChanged } =
// 			useCart(cart.value.hasOwnProperty(producer.value.id) ?
// 				producer.value.products.reduce(function(accum, product) {
// 					let cart_product = cart.value[producer.value.id].product_list.find((cart_product) => cart_product.data.id === product.id)
// 					return {...accum, [product.id]: typeof cart_product !== "undefined" ? cart_product.amount : 0}
// 				}, {}) :
// 				producer.value.products.reduce(
// 					(accum, product) => ({...accum, [product.id]:0}), {}
// 				)
// 			)
</script>
