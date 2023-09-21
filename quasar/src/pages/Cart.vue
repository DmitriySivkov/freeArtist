<template>
	<div class="column absolute fit">
		<div class="row justify-center fit">
			<div class="col-xs-12 col-md-8 col-lg-7 col-xl-6 bg-white">
				<CartProductList />
			</div>
		</div>
	</div>
</template>

<script setup>
import CartProductList from "src/components/cart/CartProductList.vue"
import { useCartStore } from "src/stores/cart"
import { computed } from "vue"

const cartStore = useCartStore()

const cart = computed(() => cartStore.data)
</script>
