<script setup>
const props = defineProps({
	order: Object,
	cardClass: String
})

const emit = defineEmits([
	"show"
])

</script>

<template>
	<q-card
		square
		flat
		class="cursor-pointer q-hoverable"
		:class="cardClass"
		@click="emit('show', order)"
	>
		<span class="q-focus-helper"></span>
		<div
			class="column q-pa-md"
			style="max-width:100%; min-height: 90px"
		>
			<div class="col q-mb-xs flex flex-center">
				<span class="text-body2">#{{ order.producer_order_id }}</span>
			</div>
		</div>
	</q-card>
	<q-separator />
</template>
