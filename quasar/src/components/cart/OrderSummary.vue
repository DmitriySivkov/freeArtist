<template>
	<q-card class="full-height">
		<q-card-section class="text-center">
			Стоимость: {{ total_price }} р.
		</q-card-section>
		<q-separator />
		<q-card-section class="text-center">
			<q-btn
				label="Оформить заказ"
				color="primary"
				@click="makeNewOrder"
			/>
		</q-card-section>
	</q-card>
</template>

<script>
import { computed } from "vue"
import { useCartStore } from "src/stores/cart"
import { useOrderStore } from "src/stores/order"
export default {
	setup() {
		const cart_store = useCartStore()
		const order_store = useOrderStore()

		const cart = computed(() => cart_store.data)

		const total_price = computed(() => {
			return Object.values(cart.value).reduce(
				(accum, cart_item) =>
					accum + cart_item.product_list.reduce((ac, product) => ac + product.data.price * product.amount, 0),
				0).toFixed(2)
		})

		const makeNewOrder = () => {
			order_store.create(cart.value)
		}

		return {
			total_price,
			makeNewOrder
		}
	}
}
</script>
