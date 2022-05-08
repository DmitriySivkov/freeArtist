<template>
	<q-footer elevated>
		<q-toolbar class="q-mb-md q-mt-md q-gutter-sm">
			<q-btn
				stretch
				:label="cartCounter"
				icon-right="shopping_cart"
				class="text-right"
				to="/cart"
			/>
		</q-toolbar>
	</q-footer>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { LocalStorage } from "quasar"
export default {
	setup() {
		const $store = useStore()
		const cartCounter = computed(
			() => Object.values($store.state.cart.data)
				.reduce((accum, productList) => accum + Object.keys(productList).length, 0)
		)

		if (LocalStorage.has("cart"))
			$store.dispatch("cart/setCart", LocalStorage.getItem("cart"))

		return {
			cartCounter
		}
	}
}
</script>
