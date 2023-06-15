<script setup>
import { ref } from "vue"

const props = defineProps({
	modelValue: {
		type: Object,
		default: () => ({})
	}
})

const emit = defineEmits([
	"update:modelValue"
])

const addOption = (option, doneFn) => {
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
</script>

<template>
	<q-select
		label="Выберите тег"
		filled
		:model-value="modelValue.tags"
		use-input
		use-chips
		multiple
		hide-dropdown-icon
		input-debounce="0"
		@new-value="addOption"
	/>
</template>
