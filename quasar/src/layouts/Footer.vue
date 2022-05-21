<template>
	<q-footer elevated>
		<q-toolbar class="q-mb-md q-mt-md q-gutter-sm">
			<q-btn
				stretch
				:label="cartCounter"
				icon-right="shopping_cart"
				:color="cartCounter > 0 ? 'secondary': 'primary'"
				class="text-right"
				to="/cart"
			/>
		</q-toolbar>
	</q-footer>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
export default {
	setup() {
		const $store = useStore()
		const cartCounter = computed(
			() => Object.values($store.state.cart.data)
				.reduce((accum, cart_item) => accum + cart_item.products.length, 0)
		)

		return {
			cartCounter
		}
	}
}
</script>
