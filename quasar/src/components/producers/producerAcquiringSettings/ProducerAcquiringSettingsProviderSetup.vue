<script setup>
import { ref, onMounted } from "vue"
import { PAYMENT_PROVIDER_NAMES, PAYMENT_PROVIDER_INPUTS } from "src/const/paymentProviders.js"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"

const props = defineProps({
	modelValue: Number
})

const emit = defineEmits([
	"update:modelValue",
	"success"
])

const isLoading = ref(false)

const $router = useRouter()

const paymentProviderData = ref(
	PAYMENT_PROVIDER_INPUTS[props.modelValue]
		.reduce((acc, item) => ({...acc, [item.name]:null}), {})
)

const setPaymentProvider = () => {
	isLoading.value = true

	const promise = api.post(
		`personal/producers/${$router.currentRoute.value.params.producer_id}/payment-providers`,
		{
			payment_provider_id: props.modelValue,
			payment_provider_data: paymentProviderData.value
		}
	)

	promise.catch((error) => {
		// todo
	})

	promise.then((response) => {
		// todo
		emit("success")
	})

	promise.finally(() => isLoading.value = false)
}

const isMounting = ref(true)

onMounted(() => {
	const promise = api.get(
		`personal/producers/${$router.currentRoute.value.params.producer_id}/payment-providers`,
		{
			params: {
				payment_provider_id: props.modelValue
			}
		}
	)

	promise.catch((error) => {
		// todo
	})

	promise.then((response) => {
		if (response.data) {
			paymentProviderData.value = response.data
		}
	})

	promise.finally(() => isMounting.value = false)

})
</script>

<template>
	<div class="row q-mb-sm">
		<div class="col">
			<q-icon
				name="keyboard_backspace"
				size="md"
				class="cursor-pointer"
				@click="emit('update:modelValue', null)"
			/>
		</div>
	</div>

	<div class="rounded-borders bg-primary text-white q-pa-md q-mb-md text-body1">
		{{ PAYMENT_PROVIDER_NAMES[modelValue] }}
	</div>

	<q-form @submit="setPaymentProvider">
		<q-input
			v-for="(inputData, index) in PAYMENT_PROVIDER_INPUTS[modelValue]"
			:key="index"
			filled
			:label="inputData.label"
			:type="inputData.type"
			:disable="isLoading || isMounting"
			v-model="paymentProviderData[inputData.name]"
			class="q-mb-xs"
		/>

		<q-btn
			label="Продолжить"
			type="submit"
			color="primary"
			class="q-pa-lg q-mt-md full-width text-body1"
			:loading="isLoading || isMounting"
		/>
	</q-form>
</template>
