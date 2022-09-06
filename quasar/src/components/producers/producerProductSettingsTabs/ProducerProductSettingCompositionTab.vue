<template>
	<q-btn
		label="Добавить ингридиент"
		color="primary"
		class="q-mb-md"
		@click="addIngredient"
	/>
	<q-card
		v-for="(ingredient, index) in composition"
		:key="index"
		flat
	>
		<q-input
			:disable="ingredient.to_delete"
			filled
			label="Название ингридиента"
			v-model="ingredient.name"
			lazy-rules
			:rules="[ val => val && val.length > 2 || 'Название ингридиента должно быть длиннее 2 символов']"
			class="q-pb-lg"
		>
			<template v-slot:after>
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
			:disable="ingredient.to_delete"
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
		const composition = ref(_.clone(props.selectedProduct.composition))

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
			composition.value.find((i, index) => index === item_index).to_delete = false

		return {
			composition,
			addIngredient,
			deleteIngredient,
			restoreIngredient
		}
	}
}
</script>
