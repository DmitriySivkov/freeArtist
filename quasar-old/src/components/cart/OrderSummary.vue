<template>
	<q-page-sticky
		expand
		position="bottom"
		class="row q-pa-xs"
	>
		<div class="col-xs-12">
			<q-card>
				<q-card-section class="text-center">
					Стоимость: {{ totalPrice }} р.
				</q-card-section>
				<q-separator />
				<q-card-actions align="center">
					<q-btn
						label="Оформить заказ"
						color="primary"
						@click="makeNewOrder"
					/>
				</q-card-actions>
			</q-card>
		</div>
	</q-page-sticky>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"
export default {
	setup() {
		const $store = useStore()
		const cart = computed(() => $store.state.cart.data)

		const totalPrice = computed(() => {
			return Object.values(cart.value).reduce(
				(accum, cart_item) =>
					accum + cart_item.products.reduce((ac, product) => ac + product.data.price * product.amount, 0),
				0).toFixed(2)
		})

		const makeNewOrder = () => {
			$store.dispatch("order/create", cart.value)
		}

		return {
			totalPrice,
			makeNewOrder
		}
	}
}
</script>
