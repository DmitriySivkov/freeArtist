<template>
	<q-footer elevated>
		<q-toolbar class="q-mb-md q-mt-md">
			<q-btn
				stretch
				:label="cartCounter"
				icon-right="shopping_cart"
				class="text-right"
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
				.reduce((accum, productList) => accum + Object.keys(productList).length, 0)
		)
		return {
			cartCounter
		}
	}
}
</script>
