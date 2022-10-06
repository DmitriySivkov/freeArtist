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
			class="q-pb-lg"
		/>

		<q-field
			filled
			v-model="product.price"
			type="number"
			label="Стоимость *"
			class="q-pb-lg"
			:rules="[ val => parseFloat(val) > 0 || 'Нужно указать стоимость']"
			lazy-rules
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
			<div class="col-xs-12">
				<q-btn
					:disable="disable_submit"
					:label="is_empty_product ? 'Создать' : 'Сохранить'"
					type="submit"
					color="primary"
					class="q-pa-lg full-width"
				/>
			</div>
			<div class="col-xs-12">
				<q-btn
					label="Сбросить"
					type="reset"
					color="warning"
					class="q-pa-lg full-width"
				/>
			</div>
			<div
				v-if="!is_empty_product"
				class="col-xs-12"
			>
				<q-btn
					label="Удалить продукт"
					color="red-5"
					class="q-pa-lg full-width"
					@click="deleteDialog"
				/>
			</div>
		</div>
	</q-form>
</template>

<script>
import { ref, computed } from "vue"
import _ from "lodash"
import { useStore } from "vuex"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useQuasar } from "quasar"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		}
	},
	emits: ["productCreated", "productDeleted"],
	setup(props, { emit }) {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()

		const product = ref(_.clone(props.selectedProduct))
		const is_empty_product = computed(() => Object.keys(props.selectedProduct).length === 0)

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
			)
				return

			disable_submit.value = true
			if (is_empty_product.value) {
				$store.dispatch("userProducer/createProducerProduct", {
					producer_id: parseInt($router.currentRoute.value.params.team_id),
					settings: {
						title: product.value.title,
						price: parseFloat(product.value.price).toFixed(2),
						amount: product.value.amount
					}
				}).then((response) => {
					emit("productCreated", response.data.id)
					notifySuccess("Продукт '" + product.value.title + "' успешно создан")
					disable_submit.value = false
				}).catch((error) => {
					notifyError(error.response.data)
					disable_submit.value = false
				})
			} else {
				$store.dispatch("userProducer/syncProducerProductCommonSettings", {
					producer_id: parseInt($router.currentRoute.value.params.team_id),
					product_id: props.selectedProduct.id,
					settings: {
						title: product.value.title,
						price: parseFloat(product.value.price).toFixed(2),
						amount: product.value.amount
					}
				}).then(() => {
					notifySuccess("Настройки продукта '" + product.value.title + "' успешно изменены")
					disable_submit.value = false
				}).catch((error) => {
					notifyError(error.response.data)
					disable_submit.value = false
				})
			}
		}

		const reset = () => {
			product.value.title = props.selectedProduct.title
			product.value.price = props.selectedProduct.price
			product.value.amount = props.selectedProduct.amount
		}

		const deleteDialog = () => {
			$q.dialog({
				title: "Подтверждение",
				message: "Удалить продукт: " + product.value.title + " ?",
				cancel: true,
			}).onOk(() => {
				$store.dispatch("userProducer/deleteProducerProduct", {
					producer_id: parseInt($router.currentRoute.value.params.team_id),
					product_id: props.selectedProduct.id,
				}).then(() => {
					notifySuccess("Продукт '" + product.value.title + "' успешно удалён")
					emit("productDeleted", props.selectedProduct.id)
				}).catch((error) => {
					notifyError(error.response.data)
				})
			})
		}

		return {
			product,
			money_config,
			reset,
			submit,
			disable_submit,
			is_empty_product,
			deleteDialog
		}
	}
}
</script>
