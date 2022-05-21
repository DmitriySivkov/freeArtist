<template>
	<ProductList :style="{marginBottom: cartPageMargin + 'px'}"/>
	<OrderSummary
		ref="cartOrderSummaryComponent"
		v-if="cart.length > 0"
	/>
	<router-view />
</template>

<script>
import ProductList from "src/components/cart/ProductList"
import OrderSummary from "src/components/cart/OrderSummary"
import { useStore } from "vuex"
import { computed, onMounted, ref } from "vue"
export default {
	components: { ProductList, OrderSummary },
	setup() {
		const $store = useStore()
		const cart = computed(() => Object.values($store.state.cart.data))

		const cartOrderSummaryComponent = ref(null)

		const cartPageMargin = ref(0)

		onMounted(() => {
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
