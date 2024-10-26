<script setup>
import { ref } from "vue"
import { PAYMENT_PROVIDER_NAMES, PAYMENT_PROVIDER_INPUTS } from "src/const/paymentProviders"
import { api } from "src/boot/axios"
import { useNotification } from "src/composables/notification"

const props = defineProps({
	modelValue: Number,
	producerId: Number,
	producerPaymentProviders: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits([
	"update:modelValue",
	"success"
])

const { notifyError, notifySuccess } = useNotification()

const isLoading = ref(false)

const filledProvider =
	props.producerPaymentProviders?.find((pp) =>
		pp.payment_provider_id === props.modelValue
	)


const paymentProviderData = ref(
	PAYMENT_PROVIDER_INPUTS[props.modelValue].map((inputData) =>
		({
			...inputData,
			value: filledProvider ? filledProvider.payment_provider_data[inputData.name] : null,
		})
	)
)

const paymentProviderActivity = ref(
	filledProvider ? !!filledProvider.is_active : false
)

const setPaymentProvider = () => {
	isLoading.value = true

	let payment_provider_data = paymentProviderData.value.reduce((acc, item) =>
		({...acc, [item.name]: item.value}), {}
	)

	const promise = api.post(
		`personal/producers/${props.producerId}/payment-providers`,
		{
			payment_provider_id: props.modelValue,
			payment_provider_data: payment_provider_data,
			payment_provider_activity: paymentProviderActivity.value
		}
	)

	promise.catch(() => {
		notifyError("Не удалось")
	})

	promise.then((response) => {
		notifySuccess(`Обновлено: ${PAYMENT_PROVIDER_NAMES[props.modelValue]}`)

		emit("success", response.data)
	})

	promise.finally(() => isLoading.value = false)
}
</script>

<template>
	<div class="col-auto row q-mb-sm">
		<div class="col">
			<q-icon
				name="keyboard_backspace"
				size="md"
				class="cursor-pointer"
				@click="emit('update:modelValue', null)"
			/>
		</div>
	</div>

	<div class="col-auto rounded-borders bg-primary text-white q-pa-md q-mb-md text-body1">
		{{ PAYMENT_PROVIDER_NAMES[modelValue] }}
	</div>

	<q-form
		class="col column no-wrap"
		@submit="setPaymentProvider"
	>
		<q-input
			v-for="(inputData, index) in paymentProviderData"
			:key="index"
			filled
			:label="inputData.label"
			:type="inputData.type"
			:disable="isLoading"
			v-model="inputData.value"
			class="col-auto q-mb-xs"
		/>

		<q-item
			tag="label"
			clickable
			class="col-auto q-pa-none rounded-borders q-mt-sm q-pa-sm"
			:active="paymentProviderActivity"
			active-class="bg-primary text-white"
			:disable="isLoading"
		>
			<q-item-section side>
				<q-checkbox v-model="paymentProviderActivity" />
			</q-item-section>
			<q-item-section>
				<q-item-label class="text-body1">Активность</q-item-label>
			</q-item-section>
		</q-item>

		<div class="col content-end">
			<q-btn
				label="Продолжить"
				type="submit"
				color="primary"
				class="q-pa-lg q-mt-md full-width text-body1"
				:loading="isLoading"
			/>
		</div>
	</q-form>
</template>
