<script setup>
import { onMounted, ref } from "vue"
import { api } from "src/boot/axios"
import {Dialog} from "quasar"
import ShowPaymentPageDialog from "src/components/dialogs/ShowPaymentPageDialog.vue"
import OrderInvalidProductDialog from "src/components/dialogs/OrderInvalidProductDialog.vue"

const props = defineProps({
	producerId: Number
})

const selectedPaymentMethod = ref(null)
const paymentMethods = ref([])

const selectPaymentMethod = (paymentMethodId) => {
	selectedPaymentMethod.value = paymentMethodId
}

onMounted(() => {
	const promise = api.get(`checkout/${props.producerId}`)

	promise.then((response) => {
		paymentMethods.value = response.data.payment_methods
	})
})

// const makeOrder = async (producerId) => {
// 	// todo - integrate with reworked 'init()' function
// 	if (userStore.is_logged) {
// 		isLoading.value = true
//
// 		const producerOrderObject = cart.value.find((i) => i.producer_id === producerId)
//
// 		const promise = api.post("yookassa/create", {
// 			producer_id: producerId,
// 			price: totalPrice.value[producerId],
// 			products: producerOrderObject.products.map((p) => ({
// 				id: p.data.id,
// 				price: p.data.price,
// 				amount: p.cart_amount
// 			})),
// 			payment_method: paymentMethods.value[producerId].selectedPaymentMethodId
// 		})
//
// 		promise.then((response) => {
// 			Dialog.create({
// 				component: ShowPaymentPageDialog,
// 				componentProps: {
// 					confirmationToken: response.data.confirmation.confirmation_token
// 				}
// 			})
// 				.onOk(() => {
// 					orderAction({
// 						transactionUuid: response.data.transaction_uuid,
// 						orderMeta: null
// 					})
// 				})
// 				.onCancel(() => {
// 					notifyError("Не получилось оплатить заказ")
// 				})
// 		})
//
// 		promise.catch((error) => {
// 			if (
// 				typeof error.response.data === "object" &&
// 				error.response.data.exception === "App\\Exceptions\\OrderInvalidItemsException"
// 			) {
// 				Dialog.create({
// 					component: OrderInvalidProductDialog,
// 					componentProps: {
// 						message: error.response.data.message,
// 						invalidProducts: error.response.data.invalid_items
// 					}
// 				}).onDismiss(() => {
// 					isCartLoaded.value = false
// 					init()
// 				})
//
// 				return
// 			}
//
// 			notifyError(error.response.data.message)
// 		})
//
// 		promise.finally(() => isLoading.value = false)
// 	} else {
// 		// todo
// 		// Dialog.create({
// 		// 	component: OrderMetaDialog,
// 		// }).onOk((orderMeta) => {
// 		// 	orderAction({ producerId, orderMeta })
// 		// })
// 	}
// }
</script>

<template>
	<div
		v-for="method in paymentMethods"
		:key="method.id"
		class="col-6 flex"
	>
		<q-card
			class="q-py-md full-width text-body1 text-center q-hoverable cursor-pointer"
			:class="{'bg-primary text-white': method.id === selectedPaymentMethod}"
			@click="selectPaymentMethod(method.id)"
		>
			<span class="q-focus-helper"></span>
			<div class="row">
				<q-card-section class="col-3">
					<q-radio
						:model-value="selectedPaymentMethod"
						checked-icon="radio_button_checked"
						unchecked-icon="radio_button_unchecked"
						:color="selectedPaymentMethod === method.id ? 'white' : 'black'"
						:name="method.id"
						:val="method.id"
						@update:model-value="selectedPaymentMethod"
					/>
				</q-card-section>
				<q-card-section class="col-9 self-center">
					<span class="text-body1">{{ method.name }}</span>
				</q-card-section>
			</div>
		</q-card>
	</div>
	<div class="row">
		<q-btn
			class="q-py-md full-width"
			label="Оформить заказ"
			color="primary"
		/>
	</div>
</template>
