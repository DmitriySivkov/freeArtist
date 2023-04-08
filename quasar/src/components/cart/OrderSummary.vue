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
<!-- todo - redirect to user orders page on new order -->
<script>
import { computed } from "vue"
import { useCartStore } from "src/stores/cart"
import { useOrderStore } from "src/stores/order"
import { useNotification } from "src/composables/notification"
import { Loading } from "quasar"
import { useCart } from "src/composables/cart"
export default {
	setup() {
		const cart_store = useCartStore()
		const order_store = useOrderStore()
		const { notifySuccess } = useNotification()
		const { clearCart } = useCart()

		const cart = computed(() => cart_store.data)

		const total_price = computed(() => {
			return Object.values(cart.value).reduce(
				(accum, cart_item) =>
					accum + cart_item.product_list.reduce((ac, product) => ac + product.data.price * product.amount, 0),
				0).toFixed(2)
		})

		const makeNewOrder = () => {
			Loading.show({ spinnerColor: "primary" })

			const promise = order_store.create(cart.value)

			promise.then(() => {
				clearCart()

				Loading.hide()

				notifySuccess("Заказ успешно оформлен")
			})
		}

		return {
			total_price,
			makeNewOrder
		}
	}
}
</script>
