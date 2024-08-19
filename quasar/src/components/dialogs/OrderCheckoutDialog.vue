<script setup>
import { ref, computed } from "vue"
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

const makeTransaction = async () => {
	isLoading.value = true

	const promise = api.post("orders/transaction", {
		producer_id: props.orderData.producer_id,
		price: props.totalPrice,
		products: props.orderData.products.map((p) => ({
			id: p.data.id,
			price: p.data.price,
			amount: p.cart_amount
		})),
		payment_method: formData.value.paymentMethod,
		order_date: orderDate.value
	})

	promise.then((response) => {
		const transactionData = response.data

		if (formData.value.paymentMethod === PAYMENT_METHODS.CASH) {
			cashAction(transactionData)
		}

		if (formData.value.paymentMethod === PAYMENT_METHODS.CARD) {
			cardAction(transactionData)
		}

		setOrderCookie(transactionData.transaction_uuid)
	})

	promise.catch((error) => {
		onDialogOK({
			error: error.response.data
		})
	})
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
	orderAction({
		transactionUuid: transactionData.transaction_uuid
	})
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

const formData = ref({
	phone: null,
	timePeriod: null,
	paymentMethod: null
})

const today = date.formatDate(new Date(), "YYYY-MM-DD")

const orderDate = ref(today)

const selectPaymentMethod = (paymentMethodId) => {
	formData.value.paymentMethod = paymentMethodId
}

const selectTimePeriod = (timePeriodId) => {
	formData.value.timePeriod = timePeriodId

	if (timePeriodId === ORDER_TIME_PERIODS.ASAP) {
		orderDate.value = today
	}
}

const timePeriod = ref(null)
const paymentMethod = ref(null)

const validatePhone = (phone) => {
	if (!phone || `8${phone}`.length !== 11) {
		return false
	}

	return true
}
// todo - wrap all necessary fields inside form in q-field for validation (calendar / payment method)
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-sm">
			<q-form
				greedy
				@submit="makeTransaction"
			>
				<template v-if="!userStore.is_logged">
					<div class="text-h6">Ваш номер телефона</div>
					<q-input
						filled
						type="tel"
						v-model="formData.phone"
						mask="8 (###) ###-##-##"
						fill-mask=""
						lazy-rules
						unmasked-value
						:rules="[validatePhone]"
					/>
				</template>

				<q-item
					dense
					class="q-pa-none"
				>
					<q-item-section class="text-h6">Когда должно быть готово ?</q-item-section>
					<q-item-section
						v-if="timePeriod?.hasError"
						avatar
					>
						<q-icon
							color="red-9"
							name="error"
						/>
					</q-item-section>
				</q-item>
				<q-separator
					:class="{'invisible': !timePeriod?.hasError}"
					color="red-10"
					size="2px"
				/>

				<q-field
					ref="timePeriod"
					:model-value="formData.timePeriod"
					lazy-rules
					:rules="[
						(val) => !!val,
					]"
					borderless
					no-error-icon
					class="q-pb-none"
				>
					<template #control>
						<div class="row q-col-gutter-xs">
							<div
								v-for="period in ORDER_TIME_PERIODS"
								:key="period"
								class="col-6"
							>
								<q-card
									class="q-py-md full-width text-body1 text-center q-hoverable cursor-pointer"
									:class="[
										{'bg-primary text-white': period === formData.timePeriod},
									]"
									@click="selectTimePeriod(period)"
								>
									<span class="q-focus-helper"></span>
									<div class="row">
										<q-card-section class="col-3">
											<q-radio
												:model-value="formData.timePeriod"
												checked-icon="radio_button_checked"
												unchecked-icon="radio_button_unchecked"
												:color="formData.timePeriod === period ? 'white' : 'black'"
												:val="period"
											/>
										</q-card-section>
										<q-card-section class="col-9 self-center">
											<span class="text-body1">{{ ORDER_TIME_PERIOD_NAMES[period] }}</span>
										</q-card-section>
									</div>
								</q-card>
							</div>
						</div>
					</template>
				</q-field>
				<div
					v-if="formData.timePeriod === ORDER_TIME_PERIODS.SPECIFIC_DATE"
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

				<q-item
					dense
					class="q-pa-none q-mt-sm"
				>
					<q-item-section class="text-h6">Способ оплаты</q-item-section>
					<q-item-section
						v-if="paymentMethod?.hasError"
						avatar
					>
						<q-icon
							color="red-9"
							name="error"
						/>
					</q-item-section>
				</q-item>
				<q-separator
					:class="{'invisible': !paymentMethod?.hasError}"
					color="red-10"
					size="2px"
				/>
				<q-field
					ref="paymentMethod"
					:model-value="formData.paymentMethod"
					lazy-rules
					:rules="[
						(val) => !!val,
					]"
					borderless
					no-error-icon
					class="q-pb-none"
				>
					<template #control>
						<div class="row q-col-gutter-xs">
							<div
								v-for="method in paymentMethods"
								:key="method.id"
								class="col-6"
							>
								<q-card
									class="q-py-md full-width text-body1 text-center q-hoverable cursor-pointer"
									:class="{'bg-primary text-white': method.id === formData.paymentMethod}"
									@click="selectPaymentMethod(method.id)"
								>
									<span class="q-focus-helper"></span>
									<div class="row">
										<q-card-section class="col-3">
											<q-radio
												:model-value="formData.paymentMethod"
												checked-icon="radio_button_checked"
												unchecked-icon="radio_button_unchecked"
												:color="formData.paymentMethod === method.id ? 'white' : 'black'"
												:val="method.id"
											/>
										</q-card-section>
										<q-card-section class="col-9 self-center">
											<span class="text-body1">{{ method.name }}</span>
										</q-card-section>
									</div>
								</q-card>
							</div>
						</div>
					</template>
				</q-field>

				<div class="row q-mt-md">
					<q-btn
						class="q-py-md full-width"
						label="Продолжить"
						color="primary"
						type="submit"
					/>
				</div>
			</q-form>
			<q-inner-loading :showing="isLoading">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</q-dialog>
</template>
