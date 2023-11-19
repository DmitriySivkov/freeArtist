<template>
	<div class="row q-mb-sm">
		<div class="col-xs-12 col-sm-auto">
			<q-btn
				label="Добавить ингредиент"
				color="green-4"
				class="q-pa-md text-body2 full-width"
				@click="addIngredient"
			/>
		</div>
	</div>
	<q-form ref="form">
		<q-card
			v-for="(ingredient, index) in modelValue.composition"
			:key="index"
			class="composition__card border-primary q-mb-sm"
			bordered
		>
			<q-input
				:disable="ingredient.to_delete"
				borderless
				label="Название ингредиента"
				v-model="ingredient.name"
				:rules="[ val => !!val ]"
				lazy-rules="ondemand"
				class="q-pb-none q-pl-sm bg-grey-2"
			>
				<template v-slot:after>
					<q-btn
						v-if="ingredient.to_delete"
						flat
						square
						icon="restore"
						class="bg-green-4 text-white full-height"
						@click="restoreIngredient(index)"
						style="border-top-right-radius: 4px"
					/>
					<q-btn
						v-else
						flat
						square
						icon="clear"
						class="bg-red text-white full-height"
						@click="removeIngredient(index)"
						style="border-top-right-radius: 4px"
					/>

				</template>
			</q-input>

			<q-separator color="primary" />

			<q-input
				:disable="ingredient.to_delete"
				class="q-px-sm bg-grey-4"
				borderless
				type="textarea"
				label="Описание ингредиента (необязательно)"
				v-model="ingredient.description"
			/>
		</q-card>
	</q-form>
</template>

<script setup>
import { ref } from "vue"
import { clone } from "lodash"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	},
})

const emit = defineEmits([
	"update:modelValue"
])

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

	let item = clone(props.modelValue.composition.find((i, index) => index === item_index))
	item.to_delete = true

	let composition = props.modelValue.composition.filter((i, index) => index !== item_index)

	composition.splice(item_index, 0, item)

	emit("update:modelValue", {...props.modelValue, composition })
}

const restoreIngredient = (item_index) => {
	let item = clone(props.modelValue.composition.find((i, index) => index === item_index))
	delete item.to_delete

	let composition = props.modelValue.composition.filter((i, index) => index !== item_index)

	composition.splice(item_index, 0, item)

	emit("update:modelValue", {...props.modelValue, composition })
}
</script>
