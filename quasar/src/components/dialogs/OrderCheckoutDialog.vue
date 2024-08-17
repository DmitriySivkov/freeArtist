<script setup>
import { ref } from "vue"
import { api } from "src/boot/axios"
import { Dialog, date, useDialogPluginComponent, Cookies } from "quasar"
import ShowYookassaPaymentPageDialog from "src/components/dialogs/ShowYookassaPaymentPageDialog.vue"
import { ORDER_TIME_PERIODS, ORDER_TIME_PERIOD_NAMES } from "src/const/orderTimePeriods"
import { PAYMENT_METHODS } from "src/const/paymentMethods"
import { PAYMENT_PROVIDERS } from "src/const/paymentProviders"
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"

const props = defineProps({
	totalPrice: String,
	orderData: Object,
	paymentMethods: Object,
	paymentProviderId: {
		type: Number,
		required: false
	}
})

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const { notifyError } = useNotification()

const userStore = useUserStore()

const isLoading = ref(false)

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

const makeTransaction = async () => {
	if (userStore.is_logged) {
		isLoading.value = true

		const promise = api.post("orders/transaction", {
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
			const transactionData = response.data

			if (selectedPaymentMethod.value === PAYMENT_METHODS.CASH) {
				cashAction(transactionData)
			}

			if (selectedPaymentMethod.value === PAYMENT_METHODS.CARD) {
				cardAction(transactionData)
			}

			setOrderCookie(transactionData.transaction_uuid)
		})

		promise.catch((error) => {
			onDialogOK({
				error: error.response.data
			})
		})
	} else {
		// todo
		// Dialog.create({
		// 	component: OrderMetaDialog,
		// }).onOk((orderMeta) => {
		// 	orderAction({ producerId, orderMeta })
		// })
	}
}

const cardAction = (transactionData) => {
	if (props.paymentProviderId === PAYMENT_PROVIDERS.YOOKASSA) {
		Dialog.create({
			component: ShowYookassaPaymentPageDialog,
			componentProps: {
				confirmationToken: transactionData.confirmation.confirmation_token
			}
		})
			.onOk(() => {
				// к этому моменту уже была проведена оплата
				orderAction({
					transactionUuid: transactionData.transaction_uuid
				})
			})
			.onCancel(() => onDialogOK({
				error: {message: "Не получилось оплатить заказ"}
			}))
	}
}

const cashAction = (transactionData) => {
	console.log("cash action: " + transactionData)
}

const orderAction = ({ transactionUuid, orderMeta }) => {
	isLoading.value = true

	const promise = api.post("orders", {
		transaction_uuid: transactionUuid,
		meta: orderMeta
	})

	promise.then((response) => {
		// если оплата картой, то к этому моменту оплата уже произведена
		// даже в случае ошибки показываем юзеру сообщение об успехе
		// обрабатываем на бэке несозданный заказ отталкиваясь от транзакции
		onDialogOK(response)
	})

	promise.catch((error) => {
		onDialogOK({
			error: error.response.data
		})
	})

	promise.finally(() => isLoading.value = false)
}

const setOrderCookie = (orderUuid) => {
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
					@click="makeTransaction"
				/>
			</div>
			<q-inner-loading :showing="isLoading">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</q-dialog>
</template>
