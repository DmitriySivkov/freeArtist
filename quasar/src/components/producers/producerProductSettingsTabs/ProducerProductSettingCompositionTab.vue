<template>
	<q-btn
		v-if="isAbleToManageProduct"
		label="Добавить ингридиент"
		color="primary"
		class="q-mb-md"
		@click="addIngredient"
	/>
	<q-form
		@submit="submit"
		@reset="reset"
	>
		<q-card
			v-for="(ingredient, index) in composition"
			:key="index"
			flat
		>
			<q-input
				:disable="ingredient.to_delete || !isAbleToManageProduct"
				filled
				label="Название ингридиента"
				v-model="ingredient.name"
				lazy-rules
				:rules="[ val => val.length >= 2 || 'Название должно быть длиннее 2 символов']"
				class="q-pb-lg"
			>
				<template
					v-slot:after
					v-if="isAbleToManageProduct"
				>

					<q-btn
						v-if="ingredient.to_delete"
						flat
						square
						icon="restore"
						class="bg-secondary text-white full-height"
						@click="restoreIngredient(index)"
					/>
					<q-btn
						v-else
						flat
						square
						icon="clear"
						class="bg-red text-white full-height"
						@click="deleteIngredient(index)"
					/>

				</template>
			</q-input>
			<q-input
				:disable="ingredient.to_delete || !isAbleToManageProduct"
				filled
				type="textarea"
				label="Описание ингридиента (необязательно)"
				v-model="ingredient.description"
			/>

			<q-separator
				v-if="index !== composition.length-1"
				class="q-mt-sm q-mb-sm bg-primary"
			/>
		</q-card>
		<div
			v-if="isAbleToManageProduct"
			class="row q-col-gutter-sm q-mt-md"
		>
			<div class="col-xs-12">
				<q-btn
					:disable="disable_submit"
					label="Сохранить"
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
		</div>
	</q-form>
</template>

<script>
import { ref } from "vue"
import _ from "lodash"
import { useNotification } from "src/composables/notification"
import { useRouter } from "vue-router"
import { useStore } from "vuex"
export default {
	props: {
		selectedProduct: {
			type: Object,
			default: () => ({})
		},
		isAbleToManageProduct: Boolean
	},
	setup(props) {
		const $store = useStore()
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()

		const composition = ref(
			props.selectedProduct.hasOwnProperty("composition") ?
				_.cloneDeep(props.selectedProduct.composition) :
				[]
		)
		const disable_submit = ref(false)

		const addIngredient = () =>
			composition.value.unshift({name:"", description:"", is_new:true})

		const deleteIngredient = (item_index) => {
			if (composition.value[item_index].is_new) {
				composition.value = composition.value.filter((i, index) => index !== item_index)
				return
			}
			composition.value.find((i, index) => index === item_index).to_delete = true
		}

		const restoreIngredient = (item_index) =>
			delete composition.value.find((i, index) => index === item_index).to_delete

		const submit = () => {
			if (
				JSON.stringify(composition.value) === JSON.stringify(props.selectedProduct.composition)
			)
				return

			disable_submit.value = true
			$store.dispatch("producer/syncProducerProductCompositionSettings", {
				producer_id: parseInt($router.currentRoute.value.params.team_id),
				product_id: props.selectedProduct.id,
				composition: composition.value
			}).then(() => {
				composition.value = _.cloneDeep(props.selectedProduct.composition)
				notifySuccess("Состав продукта '" + props.selectedProduct.title + "' успешно изменен")
				disable_submit.value = false
			}).catch((error) => {
				notifyError(error.response.data)
				disable_submit.value = false
			})

		}

		const reset = () => {
			composition.value = _.cloneDeep(props.selectedProduct.composition)
		}

		return {
			composition,
			addIngredient,
			deleteIngredient,
			restoreIngredient,
			disable_submit,
			submit,
			reset
		}
	}
}
</script>
