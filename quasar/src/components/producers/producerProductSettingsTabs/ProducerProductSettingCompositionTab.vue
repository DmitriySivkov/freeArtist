<template>
	<q-btn
		icon="add"
		color="primary"
		class="q-mb-md"
		@click="addIngredient"
	/>

	<q-form ref="form">
		<q-card
			v-for="(ingredient, index) in modelValue.composition"
			:key="index"
			class="composition__card border-primary"
			:class="{'q-my-sm': index !== modelValue.composition.length-1}"
			bordered
		>
			<q-input
				:disable="ingredient.to_delete"
				filled
				square
				label="Название ингредиента"
				v-model="ingredient.name"
				:rules="[ val => !!val || 'Укажите название']"
				lazy-rules="ondemand"
				class="q-pb-none"
			>
				<template v-slot:after>
					<q-btn
						v-if="ingredient.to_delete"
						flat
						icon="restore"
						class="bg-secondary text-white full-height"
						@click="restoreIngredient(index)"
					/>
					<q-btn
						v-else
						flat
						icon="clear"
						class="bg-red text-white full-height"
						@click="removeIngredient(index)"
					/>

				</template>
			</q-input>

			<q-separator class="bg-primary q-my-xs" />

			<q-input
				:disable="ingredient.to_delete"
				filled
				square
				type="textarea"
				label="Описание ингредиента (необязательно)"
				v-model="ingredient.description"
			/>
		</q-card>
	</q-form>
</template>

<script>
import { ref } from "vue"
import _ from "lodash"
export default {
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

		const addIngredient = () => {
			let composition = [{ is_new: true, name: "", description: "" }]

			if (!!props.modelValue.composition) {
				composition = [...composition, ...props.modelValue.composition]
			}

			emit("update:modelValue", {
				...props.modelValue,
				composition
			})
		}

		const removeIngredient = (item_index) => {
			if (!!props.modelValue.composition[item_index].is_new) {
				let composition = props.modelValue.composition.filter((i, index) => index !== item_index)

				emit("update:modelValue", {...props.modelValue, composition })
				return
			}

			let item = _.clone(props.modelValue.composition.find((i, index) => index === item_index))
			item.to_delete = true

			let composition = props.modelValue.composition.filter((i, index) => index !== item_index)

			composition.splice(item_index, 0, item)

			emit("update:modelValue", {...props.modelValue, composition })
		}

		const restoreIngredient = (item_index) => {
			let item = _.clone(props.modelValue.composition.find((i, index) => index === item_index))
			delete item.to_delete

			let composition = props.modelValue.composition.filter((i, index) => index !== item_index)

			composition.splice(item_index, 0, item)

			emit("update:modelValue", {...props.modelValue, composition })
		}

		return {
			addIngredient,
			removeIngredient,
			restoreIngredient,
			validate,
			form
		}
	}
}
</script>
