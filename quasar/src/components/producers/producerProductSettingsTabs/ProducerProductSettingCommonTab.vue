<template>
	<q-input
		filled
		label="Название *"
		:model-value="modelValue.title"
		@update:model-value="commonPropChanged({title: $event})"
		lazy-rules
		:rules="[ val => !!val || 'Укажите название']"
		class="q-pb-lg"
	/>


	<q-field
		filled
		:model-value="modelValue.price"
		@update:model-value="commonPropChanged({price: $event})"
		label="Стоимость *"
		class="q-pb-lg"
		:rules="[ val => parseFloat(val) > 0 || 'Нужно указать стоимость']"
		lazy-rules
	>
		<template v-slot:control="{ emitValue }">
			<CurrencyInput
				:model-value="parseFloat(modelValue.price)"
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
		@update:model-value="commonPropChanged({amount: parseInt($event)})"
	/>
</template>

<script>
import CurrencyInput from "src/components/helpers/CurrencyInput.vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useProducerStore } from "src/stores/producer"
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
		const $router = useRouter()
		const producer_store = useProducerStore()
		const { notifySuccess, notifyError } = useNotification()

		// const is_empty_product = computed(() => Object.keys(props.selectedProduct).length === 0)

		const currency_config = {
			currency: "RUB"
		}

		const commonPropChanged = (field) => {
			emit("update:modelValue", Object.assign(props.modelValue, field))
		}

		// const submit = () => {
		// 	if (
		// 		product.value.title.trim() === props.selectedProduct.title &&
		// 		parseFloat(product.value.price) === parseFloat(props.selectedProduct.price) &&
		// 		product.value.amount === props.selectedProduct.amount
		// 	)
		// 		return
		//
		// 	if (is_empty_product.value) {
		// 		producer_store.createProducerProduct({
		// 			producer_id: parseInt($router.currentRoute.value.params.producer_id),
		// 			settings: {
		// 				title: product.value.title,
		// 				price: parseFloat(product.value.price).toFixed(2),
		// 				amount: product.value.amount
		// 			}
		// 		})
		// 			.then((response) => {
		// 				notifySuccess("Продукт '" + product.value.title + "' успешно создан")
		// 			})
		// 			.catch((error) => {
		// 				notifyError(error.response.data)
		// 			})
		// 	} else {
		// 		producer_store.syncProducerProductCommonSettings({
		// 			producer_id: parseInt($router.currentRoute.value.params.producer_id),
		// 			product_id: props.selectedProduct.id,
		// 			settings: {
		// 				title: product.value.title,
		// 				price: parseFloat(product.value.price).toFixed(2),
		// 				amount: product.value.amount
		// 			}
		// 		})
		// 			.then(() => {
		// 				notifySuccess("Настройки продукта '" + product.value.title + "' успешно изменены")
		// 			})
		// 			.catch((error) => {
		// 				notifyError(error.response.data)
		// 			})
		// 	}
		// }

		return {
			currency_config,
			commonPropChanged
		}
	}
}
</script>
