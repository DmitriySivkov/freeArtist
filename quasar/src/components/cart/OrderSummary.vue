<template>
	<q-card>
		<q-card-section class="text-center text-body1">
			Стоимость: {{ totalPrice }} ₽
		</q-card-section>
		<q-separator />
		<q-card-section class="text-center">
			<q-btn
				class="q-pa-md"
				label="Оформить заказ"
				color="primary"
				@click="makeNewOrder"
			/>
		</q-card-section>
	</q-card>
</template>
<!-- todo - redirect to user orders page on new order -->
<script setup>
import { computed } from "vue"
import { useCartStore } from "src/stores/cart"
import { useOrderStore } from "src/stores/order"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"

const cartStore = useCartStore()
const orderStore = useOrderStore()
const { notifySuccess } = useNotification()

const cart = computed(() => cartStore.data)

const totalPrice = computed(() =>
	cart.value.reduce(
		(carry, producerSet) =>
			carry + producerSet.products.reduce((c, productSet) => c + productSet.data.price * productSet.cart_amount, 0),
		0).toFixed(2)
)

const makeNewOrder = () => {
	let cartMapped = cart.value.map((producerSet) => ({
		producer_id: producerSet.producer_id,
		products: producerSet.products.map((productSet) => ({
			product_id: productSet.data.id,
			cart_amount: productSet.cart_amount
		}))
	}))

	// todo - makeNewOrder - backend
	const promise = api.post("orders", {
		cart: cartMapped
	})

	promise.then(() => {
		cartStore.clearCart()

		notifySuccess("Заказ успешно оформлен")
	})
}

</script>
