<script setup>
import { PAYMENT_PROVIDER_NAMES } from "src/const/paymentProviders.js"

const props = defineProps({
	modelValue: {
		type: Number,
		required: false
	}
})

const emit = defineEmits([
	"update:modelValue"
])
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
		</q-item>
	</q-list>
</template>
