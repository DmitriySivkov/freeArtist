<template>
	<q-input
		filled
		label="Название *"
		v-model="product.title"
		lazy-rules
		:rules="[ val => val && val.length > 3 || 'Название должно быть длиннее 3 символов']"
		class="q-pb-lg"
		:disable="!isAbleToManageProduct"
	/>

	<q-field
		filled
		v-model="product.price"
		type="number"
		label="Стоимость *"
		class="q-pb-lg"
		:rules="[ val => parseFloat(val) > 0 || 'Нужно указать стоимость']"
		lazy-rules
		:disable="!isAbleToManageProduct"
	>
		<template v-slot:control="{ floatingLabel, modelValue, emitValue }">
			<input
				class="q-field__input"
				:value="modelValue"
				@change="e => emitValue(e.target.value.replaceAll(',',''))"
				v-money="money_config"
				v-show="floatingLabel"
			>
		</template>
	</q-field>

	<q-input
		filled
		type="number"
		label="Доступное количество"
		v-model.number="product.amount"
		:disable="!isAbleToManageProduct"
	/>
</template>

<script>
import { ref, computed } from "vue"
import _ from "lodash"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useProducerStore } from "src/stores/producer"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean
	},
	setup(props) {
		const $router = useRouter()
		const producer_store = useProducerStore()
		const { notifySuccess, notifyError } = useNotification()

		const product = ref(_.clone(props.selectedProduct))
		const is_empty_product = computed(() => Object.keys(props.selectedProduct).length === 0)

		const disable_submit = ref(false)

		const money_config = {
			decimal: ".",
			thousands: ",",
			suffix: " ₽",
			precision: 2,
			masked: false
		}

		const submit = () => {
			if (
				product.value.title.trim() === props.selectedProduct.title &&
				parseFloat(product.value.price) === parseFloat(props.selectedProduct.price) &&
				product.value.amount === props.selectedProduct.amount
			)
				return

			disable_submit.value = true

			if (is_empty_product.value) {
				producer_store.createProducerProduct({
					producer_id: parseInt($router.currentRoute.value.params.producer_id),
					settings: {
						title: product.value.title,
						price: parseFloat(product.value.price).toFixed(2),
						amount: product.value.amount
					}
				})
					.then((response) => {
						notifySuccess("Продукт '" + product.value.title + "' успешно создан")
						disable_submit.value = false
					})
					.catch((error) => {
						notifyError(error.response.data)
						disable_submit.value = false
					})
			} else {
				producer_store.syncProducerProductCommonSettings({
					producer_id: parseInt($router.currentRoute.value.params.producer_id),
					product_id: props.selectedProduct.id,
					settings: {
						title: product.value.title,
						price: parseFloat(product.value.price).toFixed(2),
						amount: product.value.amount
					}
				})
					.then(() => {
						notifySuccess("Настройки продукта '" + product.value.title + "' успешно изменены")
						disable_submit.value = false
					})
					.catch((error) => {
						notifyError(error.response.data)
						disable_submit.value = false
					})
			}
		}

		return {
			product,
			money_config,
			submit,
			disable_submit,
			is_empty_product,
		}
	}
}
</script>
