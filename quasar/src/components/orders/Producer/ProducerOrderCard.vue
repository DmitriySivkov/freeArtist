<template>
	<q-card
		square
		class="text-center cursor-pointer q-hoverable"
		:class="cardClass"
		@click="emit('show', order)"
	>
		<span class="q-focus-helper"></span>
		<div class="text-body2 q-py-md">#{{ order.id }}</div>
		<q-separator inset />
		<div class="text-body2 q-py-xs">{{ order.created_time }}</div>
	</q-card>
	<q-separator />
</template>

<script setup>
const props = defineProps({
	order: Object,
	cardClass: String,
})

const emit = defineEmits([
	"show"
])

</script>
