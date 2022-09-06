<template>
	<q-form>
		<q-input
			filled
			label="Название *"
			v-model="product.title"
			lazy-rules
			:rules="[ val => val && val.length > 3 || 'Название должно быть длиннее 3 символов']"
		/>

		<q-input
			filled
			type="number"
			label="Стоимость *"
			v-model="product.price"
			lazy-rules
			:rules="[
				val => val !== null && val !== '' || 'Укажите стоимость',
			]"
		/>

		<q-input
			filled
			type="number"
			label="Доступное количество"
			v-model="product.amount"
		/>
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
		return {
			product
		}
	}
}
</script>
