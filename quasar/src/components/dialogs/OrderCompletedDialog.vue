<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<q-card-section class="q-pa-none q-mb-md text-center">
				<span class="text-h5 text-green-7">Заказ принят в обработку</span>
			</q-card-section>

			<q-card-section class="q-pa-none text-center">
				<span class="text-h6">Отслеживайте его состояние на странице заказов</span>
			</q-card-section>

			<div class="row q-mt-md">
				<div class="col-12">
					<q-btn
						label="Перейти на страницу заказов"
						color="primary"
						class="q-pa-md full-width"
						:to="{name:'user_orders'}"
					/>
				</div>
			</div>

		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"

const emit = defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>
