<script setup>
import { ref } from "vue"
import { api } from "src/boot/axios"
import { clone } from "lodash"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const options = ref([])

const createOption = (option, doneFn) => {
	// todo - validate here

	doneFn(option, "add-unique")

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: [...props.modelValue.tags, option]}
		)
	)
}

const addOption = ({ value }) => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: [...props.modelValue.tags, value]}
		)
	)
}

const removeOptionByIndex = (index) => {
	let removedOptions = clone(props.modelValue.tags).splice(index,1)
	let difference = props.modelValue.tags.filter((t) => !removedOptions.includes(t))

	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: difference}
		)
	)
}

const removeOptionById = ({ value }) => {
	emit(
		"update:modelValue",
		Object.assign(
			props.modelValue,
			{tags: props.modelValue.tags.filter((t) => t.id !== value.id)}
		)
	)
}

const loadTags = async (query, update) => {
	if (query.length < 1) return

	const response = await api.get("personal/tags", {
		params: { query }
	})

	update(() => {
		options.value = response.data.map((tag) => {
			return {
				id: tag.id,
				name: tag.name
			}
		})
	})
}
</script>

<template>
	<q-select
		label="Начните вводить название тега"
		filled
		:model-value="modelValue.tags"
		use-input
		use-chips
		multiple
		hide-dropdown-icon
		input-debounce="1000"
		:options="options"
		option-value="id"
		option-label="name"
		options-selected-class="primary"
		@filter="loadTags"
		@new-value="createOption"
		@add="addOption"
		@remove="removeOptionById"
	>
		<template v-slot:option="scope">
			<q-item v-bind="scope.itemProps">
				<q-item-section>
					<q-item-label>{{ scope.opt.name }}</q-item-label>
				</q-item-section>
			</q-item>
		</template>
		<template #selected-item="{ opt, index }">
			<q-chip
				square
				removable
				color="primary"
				text-color="white"
				:label="opt.name"
				@remove="removeOptionByIndex(index)"
			/>
		</template>
		<template #no-option>
			<q-item>
				<q-item-section class="text-grey">
					Теги не найдены
				</q-item-section>
			</q-item>
		</template>
	</q-select>
</template>
