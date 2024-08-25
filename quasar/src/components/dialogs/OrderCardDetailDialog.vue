<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card
			class="q-dialog-plugin text-body1"
			:class="cardClass"
		>
			<q-card-section class="q-py-sm">
				<div class="row q-mb-sm">
					<div class="col">#{{ order.id }}</div>
					<div class="col text-right">{{ ORDER_STATUS_NAMES[order.status] }}</div>
				</div>
				<div>
					Приготовить к: {{ order.prepare_by ?? order.created_date }}
				</div>
			</q-card-section>
			<q-separator />
			<q-card-section>
				<div class="text-right">
					для {{ order.user }}
				</div>
				<div
					v-for="product in order.products"
					:key="product.product_id"
					class="row q-py-md justify-center"
				>
					<div class="col-xs-12 col-sm-11">
						<div class="row">
							<div class="col">
								{{ product.title }}
							</div>
							<div class="col-shrink text-right">
								{{ order.order_products[product.id] }} шт
							</div>
						</div>
						<q-separator />
					</div>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { computed } from "vue"
import { useDialogPluginComponent } from "quasar"
import { ORDER_STATUS_NAMES, ORDER_CARD_STATUS_TO_CLASS } from "src/const/orderStatuses"

defineEmits([
	...useDialogPluginComponent.emits,
])

const props = defineProps({
	order: Object,
})

const cardClass = computed(() => ORDER_CARD_STATUS_TO_CLASS[props.order.status])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>
