<script setup>
import { ref } from "vue"
import { api } from "src/boot/axios"
import { Dialog, date, useDialogPluginComponent, Platform } from "quasar"
import YookassaPaymentPageDialog from "src/components/dialogs/YookassaPaymentPageDialog.vue"
import TbankPaymentPageDialog from "src/components/dialogs/TbankPaymentPageDialog.vue"
import { ORDER_TIME_PERIODS, ORDER_TIME_PERIOD_NAMES } from "src/const/orderTimePeriods"
import { PAYMENT_METHODS } from "src/const/paymentMethods"
import { PAYMENT_PROVIDERS } from "src/const/paymentProviders"
import { useUserStore } from "src/stores/user"
import { useNotification } from "src/composables/notification"
import { useScreen } from "src/composables/screen"
import { useStorage } from "src/composables/storage"

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

const userStore = useUserStore()

const storage = useStorage()

const { notifyError } = useNotification()

const { isSmallScreen } = useScreen()

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
		phone: formData.value.phone
	})

	promise.then(async(response) => {
		const transactionData = response.data

		if (formData.value.paymentMethod === PAYMENT_METHODS.CASH) {
			cashAction(transactionData)
		}

		if (formData.value.paymentMethod === PAYMENT_METHODS.CARD) {
			cardAction(transactionData)
		}

		await saveOrderToStorage(transactionData.transaction_uuid) // for getting unauth user orders
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
			component: YookassaPaymentPageDialog,
			componentProps: {
				confirmationToken: transactionData.confirmation.confirmation_token
			}
		})
			.onOk(() => {
				// к этому моменту уже была проведена оплата
				orderAction(transactionData.transaction_uuid)
			})
			.onCancel(() =>
				onDialogOK({
					error: { message: "Не получилось оплатить заказ" }
				})
			)
	}

	if (props.paymentProviderId === PAYMENT_PROVIDERS.TBANK) {
		Dialog.create({
			component: TbankPaymentPageDialog,
			// componentProps: {
			// 	confirmationToken: transactionData.confirmation.confirmation_token
			// }
		})
			.onOk(() => {
				console.log("TBank onOk") // todo
			})
			.onCancel(() =>
				onDialogOK({
					error: { message: "Не получилось оплатить заказ" }
				})
			)
	}
}

const cashAction = (transactionData) => {
	orderAction(transactionData.transaction_uuid)
}

const orderAction = (transactionUuid) => {
	isLoading.value = true

	const promise = api.post("orders", {
		transaction_uuid: transactionUuid,
		prepare_by: prepareByDate.value,
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

// todo - clear order storage after certain time to avoid overbulk ?
const saveOrderToStorage = async(orderUuid) => {
	if (await storage.has("orders")) {
		const storageOrderUuids = await storage.get("orders")

		// todo - if from mobile app theres a chance that no space available on device. Then only allow to make order after authorization
		await storage.set({
			key: "orders",
			value: [orderUuid, ...storageOrderUuids],
		})
	} else {
		await storage.set({
			key: "orders",
			value: [orderUuid],
		})
	}
}

const formData = ref({
	phone: null,
	timePeriod: null,
	paymentMethod: null
})

const today = date.formatDate(new Date(), "YYYY-MM-DD")

const prepareByDate = ref(today)

const selectPaymentMethod = (paymentMethodId) => {
	formData.value.paymentMethod = paymentMethodId
}

const selectTimePeriod = (timePeriodId) => {
	formData.value.timePeriod = timePeriodId

	if (timePeriodId === ORDER_TIME_PERIODS.ASAP) {
		prepareByDate.value = today
	}
}

const timePeriod = ref(null)
const paymentMethod = ref(null)
// todo - обработка продюсеров в корзине если один из итемов закончился / все итемы закончились

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
		:maximized="isSmallScreen"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card class="q-dialog-plugin">
			<div
				class="column q-pa-md"
				style="min-height:100%"
			>
				<div class="col-auto text-right q-mb-lg">
					<q-icon
						v-close-popup
						name="close"
						size="md"
						class="cursor-pointer"
					/>
				</div>

				<q-form
					greedy
					class="col column"
					@submit="makeTransaction"
				>
					<div
						v-if="!userStore.is_logged"
						class="col-auto"
					>
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
					</div>

					<div class="col-auto">
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
								<div class="col-12">
									<div class="row q-col-gutter-xs">
										<div
											v-for="period in ORDER_TIME_PERIODS"
											:key="period"
											class="col-6"
										>
											<q-card
												class="q-py-lg text-body1 text-center q-hoverable cursor-pointer"
												:class="[
													{'bg-primary text-white': period === formData.timePeriod},
												]"
												style="min-height:100px"
												@click="selectTimePeriod(period)"
											>
												<span class="q-focus-helper"></span>
												<div class="row items-center">
													<div class="col-3">
														<q-radio
															:model-value="formData.timePeriod"
															checked-icon="radio_button_checked"
															unchecked-icon="radio_button_unchecked"
															:color="formData.timePeriod === period ? 'white' : 'black'"
															:val="period"
														/>
													</div>
													<div class="col q-px-md">
														<span class="text-body1">{{ ORDER_TIME_PERIOD_NAMES[period] }}</span>
													</div>
												</div>
											</q-card>
										</div>
									</div>

									<div
										v-if="formData.timePeriod === ORDER_TIME_PERIODS.SPECIFIC_DATE"
										class="row q-mt-sm"
									>
										<q-date
											v-model="prepareByDate"
											class="full-width"
											no-unset
											first-day-of-week="1"
											mask="YYYY-MM-DD"
											title="Выберите день"
											:subtitle="prepareByDate"
										/>
									</div>
								</div>
							</template>
						</q-field>
					</div>

					<div class="col-auto">
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
								<div class="col-12">
									<div class="row q-col-gutter-xs">
										<div
											v-for="method in paymentMethods"
											:key="method.id"
											class="col-6"
										>
											<q-card
												class="q-py-lg text-body1 text-center q-hoverable cursor-pointer"
												:class="{'bg-primary text-white': method.id === formData.paymentMethod}"
												style="min-height:100px"
												@click="selectPaymentMethod(method.id)"
											>
												<span class="q-focus-helper"></span>
												<div class="row items-center">
													<div class="col-3">
														<q-radio
															:model-value="formData.paymentMethod"
															checked-icon="radio_button_checked"
															unchecked-icon="radio_button_unchecked"
															:color="formData.paymentMethod === method.id ? 'white' : 'black'"
															:val="method.id"
														/>
													</div>
													<div class="col q-px-md">
														<span class="text-body1">{{ method.name }}</span>
													</div>
												</div>
											</q-card>
										</div>
									</div>
								</div>
							</template>
						</q-field>
					</div>

					<div class="col content-end q-mt-md">
						<q-btn
							class="q-py-md full-width"
							label="Продолжить"
							color="primary"
							type="submit"
							:loading="isLoading"
						/>
					</div>
				</q-form>
			</div>

			<!-- todo - если размер окна больше размера экрана, то q-inner-loading не расстягивается на всю длину блока, а только на величину экрана -->
			<q-inner-loading :showing="isLoading">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</q-dialog>
</template>
