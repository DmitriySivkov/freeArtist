<template>
	<input
		ref="inputRef"
		type="text"
	/>
</template>

<script>
import { useCurrencyInput } from "vue-currency-input"

export default {
	props: {
		modelValue: Number,
		options: Object
	},
	setup(props) {
		const { inputRef } = useCurrencyInput(props.options)

		return { inputRef }
	}
}
</script>
