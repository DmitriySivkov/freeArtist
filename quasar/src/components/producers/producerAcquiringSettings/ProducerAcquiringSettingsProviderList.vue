<script setup>
import { PAYMENT_PROVIDER_NAMES } from "src/const/paymentProviders.js"

const props = defineProps({
	modelValue: {
		type: Number,
		required: false
	},
	producerPaymentProviders: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const getActiveProvider = (paymentProviderId) => {
	return props.producerPaymentProviders.find((pp) =>
		pp.payment_provider_id === paymentProviderId)?.is_active
}
</script>

<template>
	<q-list separator>
		<q-item
			v-for="(name, providerId) in PAYMENT_PROVIDER_NAMES"
			:key="providerId"
			clickable
			class="bg-primary text-white rounded-borders q-mb-xs"
			@click="emit('update:modelValue', Number(providerId))"
		>
			<q-item-section class="text-body1">
				{{ name }}
			</q-item-section>
			<q-item-section
				side
				v-if="getActiveProvider(Number(providerId))"
			>
				<q-icon
					color="white"
					name="done"
					size="sm"
				/>
			</q-item-section>
		</q-item>
	</q-list>
</template>
