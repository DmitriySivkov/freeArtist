<script setup>
defineProps({
	isLoading: {
		type: Boolean,
		default: false,
	},
})

defineEmits([
	"confirm"
])
</script>

<template>
	<q-footer class="footer q-pa-sm bg-indigo-8">
		<q-btn
			class="q-py-sm"
			:loading="isLoading"
			color="primary"
			@click="$emit('confirm')"
		>
			<q-icon
				name="done"
				size="lg"
			/>
		</q-btn>
	</q-footer>
</template>
