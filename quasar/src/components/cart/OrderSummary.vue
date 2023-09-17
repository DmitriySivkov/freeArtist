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
import { Loading } from "quasar"

const cartStore = useCartStore()
const order_store = useOrderStore()
const { notifySuccess } = useNotification()

const cart = computed(() => cartStore.data)

const totalPrice = computed(() =>
	cart.value.reduce(
		(carry, producerSet) =>
			carry + producerSet.products.reduce((c, productSet) => c + productSet.data.price * productSet.cart_amount, 0),
		0).toFixed(2)
)

const makeNewOrder = () => {
	Loading.show({ spinnerColor: "primary" })

	const promise = order_store.create(cart.value)

	promise.then(() => {
		clearCart()

		Loading.hide()

		notifySuccess("Заказ успешно оформлен")
	})
}

</script>
