<template>
	<q-form
		@submit="submit"
		@reset="reset"
	>
		<q-input
			filled
			label="Название *"
			v-model="product.title"
			lazy-rules
			:rules="[ val => val && val.length > 3 || 'Название должно быть длиннее 3 символов']"
		/>

		<q-field
			filled
			v-model.number="product.price"
			label="Стоимость *"
			class="q-pb-lg"
			:rules="[ val => val > 0 || 'Нужно указать стоимость']"
		>
			<template v-slot:control="{ floatingLabel, modelValue, emitValue }">
				<input
					class="q-field__input"
					:value="modelValue"
					@change="e => emitValue(e.target.value.replaceAll(',',''))"
					v-money="moneyConfig"
					v-show="floatingLabel"
				>
			</template>
		</q-field>

		<q-input
			filled
			type="number"
			label="Доступное количество"
			v-model.number="product.amount"
		/>
		<div class="row q-col-gutter-sm q-mt-md">
			<div class="col-xs-12 col-md-6">
				<q-btn
					label="Подтвердить"
					type="submit"
					color="primary"
					class="q-pa-lg full-width"
				/>
			</div>
			<div class="col-xs-12 col-md-6">
				<q-btn
					label="Сбросить"
					type="reset"
					color="warning"
					class="q-pa-lg full-width"
				/>
			</div>
		</div>
	</q-form>
</template>

<script>
import { ref } from "vue"
import _ from "lodash"

export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const product = ref(_.clone(props.selectedProduct))
		const moneyConfig = {
			decimal: ".",
			thousands: ",",
			prefix: "",
			suffix: " ₽",
			precision: 2,
			masked: false
		}

		const submit = () => {

		}

		const reset = () => {
			product.value.title = props.selectedProduct.title
			product.value.price = props.selectedProduct.price
			product.value.amount = props.selectedProduct.amount
		}

		return {
			product,
			moneyConfig,
			reset,
			submit
		}
	}
}
</script>
