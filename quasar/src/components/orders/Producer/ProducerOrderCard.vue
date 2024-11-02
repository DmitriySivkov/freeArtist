<script setup>
const props = defineProps({
	order: Object,
	hasMultipleAssignees: Boolean,
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
		class="cursor-pointer q-hoverable"
		:class="cardClass"
		@click="emit('show', order)"
	>
		<span class="q-focus-helper"></span>
		<div
			class="column q-pa-md"
			style="max-width:100%; min-height: 90px"
		>
			<div
				class="col q-mb-xs"
				:class="{'flex flex-center': !hasMultipleAssignees }"
			>
				<div class="text-body2">#{{ order.producer_order_id }}</div>
			</div>

			<template v-if="hasMultipleAssignees">
				<q-separator />
				<div class="col-auto q-pt-md full-width text-caption ellipsis">
					<q-icon
						name="person"
						size="14px"
						class="q-pr-xs"
					/>
					{{ order.assignee.name ?? order.assignee.phone }}
				</div>
			</template>
		</div>
	</q-card>
	<q-separator />
</template>
