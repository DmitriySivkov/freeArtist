<template>
	<q-footer elevated>
		<q-toolbar class="q-mb-md q-mt-md q-gutter-sm">
			<q-btn
				stretch
				:label="cartCounter"
				icon-right="shopping_cart"
				:color="cartCounter > 0 || route.name === 'cart' ? 'secondary': 'primary'"
				class="col-xs-auto"
				to="/cart"
			/>
			<q-btn
				stretch
				icon-right="account_circle"
				:color="route.name.includes('personal') ? 'secondary': 'primary'"
				class="col-xs-3 col-md-shrink"
				to="/personal"
			/>
		</q-toolbar>
	</q-footer>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
import { useRoute } from "vue-router"
export default {
	setup() {
		const route = useRoute()
		const $store = useStore()
		const cartCounter = computed(
			() => Object.values($store.state.cart.data)
				.reduce((accum, cart_item) => accum + cart_item.products.length, 0)
		)

		return {
			cartCounter,
			route
		}
	}
}
</script>
