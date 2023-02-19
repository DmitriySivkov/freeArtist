<template>
	<ProductList :style="{marginBottom: cartPageMargin + 'px'}"/>
	<OrderSummary
		ref="cartOrderSummaryComponent"
		v-if="cart.length > 0"
	/>
	<router-view />
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

		const cartOrderSummaryComponent = ref(null)

		const cartPageMargin = ref(0)

		onMounted(() => {
			if (cartOrderSummaryComponent.value)
				cartPageMargin.value = cartOrderSummaryComponent.value.$el.clientHeight
		})


		return {
			cart,
			cartOrderSummaryComponent,
			cartPageMargin
		}
	}
}
</script>
