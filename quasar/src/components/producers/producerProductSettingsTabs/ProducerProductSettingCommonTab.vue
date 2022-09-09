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
			v-model="product.price"
			label="Стоимость *"
			class="q-pb-lg"
			:rules="[ val => parseFloat(val) > 0 || 'Нужно указать стоимость']"
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
		/>
		<div class="row q-col-gutter-sm q-mt-md">
			<div class="col-xs-12 col-md-6">
				<q-btn
					:disable="disable_submit"
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
import { useStore } from "vuex"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => {}
		}
	},
	setup(props) {
		const $store = useStore()
		const $router = useRouter()

		const product = ref(_.clone(props.selectedProduct))
		const { notifySuccess, notifyError } = useNotification()
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
			) {
				return
			}
			disable_submit.value = true
			$store.dispatch("userProducer/syncProducerProductCommonSettings", {
				producer_id: parseInt($router.currentRoute.value.params.team_id),
				product_id: props.selectedProduct.id,
				settings: {
					title: product.value.title,
					price: parseFloat(product.value.price).toFixed(2),
					amount: product.value.amount
				}
			}).then(() => {
				notifySuccess("Данные продукта " + product.value.title + " успешно изменены")
				disable_submit.value = false
			}).catch((error) => {
				notifyError(error.response.data)
				disable_submit.value = false
			})
		}

		const reset = () => {
			product.value.title = props.selectedProduct.title
			product.value.price = props.selectedProduct.price
			product.value.amount = props.selectedProduct.amount
		}

		return {
			product,
			money_config,
			reset,
			submit,
			disable_submit
		}
	}
}
</script>
