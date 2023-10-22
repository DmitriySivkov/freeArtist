<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin bg-primary text-white">
			<q-card-section class="q-py-none">
				<q-item class="q-pa-none">
					<q-item-section
						side
						class="text-white"
					>
						#{{ order.id }}
					</q-item-section>
					<q-item-section class="text-right">
						{{ order.created_at }}
					</q-item-section>
				</q-item>
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

defineProps({
	order: Object
})

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

</script>
