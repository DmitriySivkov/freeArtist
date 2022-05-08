<template>
	<div class="row">
		<div
			v-if="Object.keys(cart).length > 0"
			class="col-xs-12"
		>
			123
		</div>
		<div v-else>
			В корзине ничего нет
		</div>
	</div>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
export default {
	setup() {
		const $store = useStore()
		const cart = computed(() => $store.state.cart.data)
		return {
			cart
		}
	}
}
</script>
