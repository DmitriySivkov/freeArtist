<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card
			class="q-dialog-plugin"
			:class="popupClass"
		>
			<q-card-section class="q-py-sm">
				<div class="row">
					<div class="col-shrink">
						#{{ order.id }}
					</div>
					<div class="col text-right">
						{{ order.created_at }}
					</div>
				</div>
			</q-card-section>
			<q-separator class="full-width" />
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
						<q-separator class="full-width" />
					</div>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"

defineEmits([
	...useDialogPluginComponent.emits,
])

const props = defineProps({
	producerId: String,
	order: Object,
	popupClass: String
})

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>
