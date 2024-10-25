<script setup>
const props = defineProps({
	order: Object,
	cardClass: String,
})

const emit = defineEmits([
	"show"
])

</script>

<template>
	<q-card
		square
		flat
		class="q-pa-lg text-center cursor-pointer q-hoverable"
		:class="cardClass"
		@click="emit('show', order)"
	>
		<span class="q-focus-helper"></span>
		<div class="text-body2 flex flex-center">#{{ order.id }}</div>
	</q-card>
	<q-separator />
</template>
