<script setup>
import { ref } from "vue"
import CurrencyInput from "src/components/helpers/CurrencyInput.vue"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	},
})

const emit = defineEmits([
	"update:modelValue"
])

defineExpose({
	validate
})

const form = ref(null)

function validate() {
	return form.value.validate()
}

const currencyConfig = {
	currency: "RUB"
}

const commonPropChanged = (field) => {
	emit("update:modelValue", Object.assign(props.modelValue, field))
}
</script>

<template>
	<q-form ref="form">
		<q-input
			filled
			label="Название *"
			:model-value="modelValue.title"
			@update:model-value="commonPropChanged({title: $event})"
			lazy-rules="ondemand"
			:rules="[ val => !!val ]"
			class="q-pb-lg"
		/>

		<q-field
			filled
			:model-value="modelValue.price"
			@update:model-value="commonPropChanged({price: $event})"
			label="Стоимость *"
			class="q-pb-lg"
			:rules="[ val => val > 0 ]"
			lazy-rules="ondemand"
		>
			<template v-slot:control="{ emitValue }">
				<CurrencyInput
					:model-value="modelValue.price"
					@update:model-value="emitValue"
					:options="currencyConfig"
					class="q-field__input"
				/>
			</template>
		</q-field>

		<q-input
			filled
			type="number"
			label="Доступное количество"
			:model-value="modelValue.amount"
			@update:model-value="commonPropChanged({amount: $event})"
		/>
	</q-form>
</template>
