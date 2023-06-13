<script setup>
import { ref } from "vue"

const tags = ref([])
</script>

<template>
	<q-select
		label="Выберите тег"
		filled
		:model-value="tags"
		use-input
		use-chips
		multiple
		hide-dropdown-icon
		input-debounce="0"
		new-value-mode="add-unique"
	/>
</template>
