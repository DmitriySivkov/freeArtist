<template>
	<q-page class="row no-wrap justify-center">
		<div class="col-12 bg-white">
			<div class="column full-height">
				<q-scroll-area
					visible
					class="col-xs-9 col-md-10"
				>
					<ProductList />
				</q-scroll-area>
				<div class="col-xs-3 col-md-2">
					<OrderSummary v-if="cart.length > 0" />
				</div>
			</div>
		</div>
	</q-page>
</template>

<script>
import ProductList from "src/components/cart/ProductList.vue"
import OrderSummary from "src/components/cart/OrderSummary.vue"
import { useCartStore } from "src/stores/cart"
import { computed, onMounted, ref } from "vue"
export default {
	components: {
		ProductList,
		OrderSummary
	},
	setup() {
		const cart_store = useCartStore()
		const cart = computed(() => Object.values(cart_store.data))

		return {
			cart
		}
	}
}
</script>
