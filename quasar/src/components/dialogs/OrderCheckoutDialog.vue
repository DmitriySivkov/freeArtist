<script setup>
import { ref } from "vue"
import { api } from "src/boot/axios"
import { Dialog, date, useDialogPluginComponent, Cookies } from "quasar"
import ShowPaymentPageDialog from "src/components/dialogs/ShowPaymentPageDialog.vue"
import OrderInvalidProductDialog from "src/components/dialogs/OrderInvalidProductDialog.vue"
import { ORDER_TIME_PERIODS, ORDER_TIME_PERIOD_NAMES } from "src/const/orderTimePeriods"
import OrderCompletedDialog from "src/components/dialogs/OrderCompletedDialog.vue"
import {PAYMENT_METHODS} from "src/const/paymentMethods"

const props = defineProps({
	totalPrice: String,
	orderData: Object,
	paymentMethods: Object,
})

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const today = date.formatDate(new Date(), "YYYY-MM-DD")

const orderDate = ref(today)

const selectedPaymentMethod = ref(null)

const selectPaymentMethod = (paymentMethodId) => {
	selectedPaymentMethod.value = paymentMethodId
}

const selectedTimePeriod = ref(null)

const selectTimePeriod = (timePeriodId) => {
	selectedTimePeriod.value = timePeriodId

	if (timePeriodId === ORDER_TIME_PERIODS.ASAP) {
		orderDate.value = today
	}
}

const makeOrder = async () => {
	if (userStore.is_logged) {
		isLoading.value = true

		const promise = api.post("orders", {
			producer_id: props.orderData.producer_id,
			price: props.totalPrice,
			products: props.orderData.products.map((p) => ({
				id: p.data.id,
				price: p.data.price,
				amount: p.cart_amount
			})),
			payment_method: selectedPaymentMethod.value,
			order_date: orderDate.value
		})

		promise.then((response) => {
			if (selectedPaymentMethod.value === PAYMENT_METHODS.CASH) {
				cashAction()
			}

			if (selectedPaymentMethod.value === PAYMENT_METHODS.CARD) {
				yookassaAction()
			}

			setOrderCookie(response.data.uuid)

			Dialog.create({
				component: OrderCompletedDialog,
			}).onDismiss(() => {
				onDialogOK(props.orderData.producer_id)
			})
		})

		promise.catch((error) => {
			if (
				typeof error.response.data === "object" &&
				error.response.data.exception === "App\\Exceptions\\OrderInvalidItemsException"
			) {
				Dialog.create({
					component: OrderInvalidProductDialog,
					componentProps: {
						message: error.response.data.message,
						invalidProducts: error.response.data.invalid_items
					}
				}).onDismiss(() => {
					isCartLoaded.value = false
					init()
				})

				return
			}

			notifyError(error.response.data.message)
		})

		promise.finally(() => isLoading.value = false)
	} else {
		// todo
		// Dialog.create({
		// 	component: OrderMetaDialog,
		// }).onOk((orderMeta) => {
		// 	orderAction({ producerId, orderMeta })
		// })
	}
}

function yookassaAction() {
	Dialog.create({
		component: ShowPaymentPageDialog,
		componentProps: {
			confirmationToken: response.data.confirmation.confirmation_token
		}
	})
		.onOk(() => {
			orderAction({
				transactionUuid: response.data.transaction_uuid,
				orderMeta: null
			})
		})
		.onCancel(() => {
			notifyError("Не получилось оплатить заказ")
		})
}

function cashAction() {
	console.log("cash action")
}

function orderAction({ transactionUuid, orderMeta }) {
	isLoading.value = true

	const promise = api.post("orders", {
		transaction_uuid: transactionUuid,
		meta: orderMeta
	})

	promise.then((response) => {

	})

	promise.catch((error) => {
		notifyError(error.response.data.message)
	})

	promise.finally(() => isLoading.value = false)
}

function setOrderCookie(orderUuid) {
	const cookieParams = {
		domain: process.env.SESSION_DOMAIN,
		sameSite: "lax",
		path: "/",
		expires: 365
	}

	if (Cookies.has("orders")) {
		const cookieOrders = Cookies.get("orders")

		Cookies.set(
			"orders",
			JSON.stringify([orderUuid, ...cookieOrders]),
			{...cookieParams}
		)
	} else {
		Cookies.set(
			"orders",
			JSON.stringify([orderUuid]),
			{...cookieParams}
		)
	}
}
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-sm">
			<div class="text-h6 q-ma-md">Когда должно быть готово ?</div>
			<div class="row q-col-gutter-xs">
				<div
					v-for="period in ORDER_TIME_PERIODS"
					:key="period"
					class="col-6"
				>
					<q-card
						class="q-py-md full-width text-body1 text-center q-hoverable cursor-pointer"
						:class="{'bg-primary text-white': period === selectedTimePeriod}"
						@click="selectTimePeriod(period)"
					>
						<span class="q-focus-helper"></span>
						<div class="row">
							<q-card-section class="col-3">
								<q-radio
									:model-value="selectedTimePeriod"
									checked-icon="radio_button_checked"
									unchecked-icon="radio_button_unchecked"
									:color="selectedTimePeriod === period ? 'white' : 'black'"
									:val="period"
									@update:model-value="selectedTimePeriod"
								/>
							</q-card-section>
							<q-card-section class="col-9 self-center">
								<span class="text-body1">{{ ORDER_TIME_PERIOD_NAMES[period] }}</span>
							</q-card-section>
						</div>
					</q-card>
				</div>
			</div>
			<div
				v-if="selectedTimePeriod === ORDER_TIME_PERIODS.SPECIFIC_DATE"
				class="row q-mt-sm"
			>
				<q-date
					v-model="orderDate"
					class="full-width"
					no-unset
					first-day-of-week="1"
					mask="YYYY-MM-DD"
					title="Выберите день"
					:subtitle="orderDate"
				/>
			</div>

			<div class="text-h6 q-ma-md">Способ оплаты</div>
			<div class="row q-col-gutter-xs">
				<div
					v-for="method in paymentMethods"
					:key="method.id"
					class="col-6"
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
			</div>
			<div class="row q-mt-sm">
				<q-btn
					class="q-py-md full-width"
					label="Продолжить"
					color="primary"
					@click="makeOrder"
				/>
			</div>
		</q-card>
	</q-dialog>
</template>
