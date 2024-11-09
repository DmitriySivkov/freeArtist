<script setup>
const props = defineProps({
	modelValue: Boolean,
	isLoading: Boolean
})

const emit = defineEmits([
	"update:modelValue",
	"showMonthlyView",
	"showWeeklyView",
])

const toggleView = () => {
	emit("update:modelValue", !props.modelValue)
}
</script>

<template>
	<q-footer class="footer q-pa-sm bg-indigo-8">
		<q-btn
			class="q-py-sm"
			color="primary"
			:loading="isLoading"
			:icon="modelValue ? 'calendar_month' : 'date_range'"
			@click="toggleView"
		/>
	</q-footer>
</template>
