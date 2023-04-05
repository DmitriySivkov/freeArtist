<template>
	<q-form ref="form">
		<q-card flat>
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
						:options="currency_config"
						class="q-field__input"
					/>
				</template>
			</q-field>

			<q-input
				filled
				type="number"
				label="Доступное количество"
				:model-value="modelValue.amount"
				@update:model-value="commonPropChanged({amount: Number($event)})"
			/>
		</q-card>
	</q-form>
</template>

<script>
import { ref } from "vue"
import CurrencyInput from "src/components/helpers/CurrencyInput.vue"
export default {
	components: {
		CurrencyInput
	},
	props: {
		modelValue: {
			type: Object,
			default: () => ({})
		},
	},
	setup(props, { emit }) {
		const form = ref(null)

		const validate = () => {
			return form.value.validate()
		}

		const currency_config = {
			currency: "RUB"
		}

		const commonPropChanged = (field) => {
			emit("update:modelValue", Object.assign(props.modelValue, field))
		}

		return {
			currency_config,
			commonPropChanged,
			validate,
			form
		}
	}
}
</script>
