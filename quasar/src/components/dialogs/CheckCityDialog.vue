<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin">
			<div class="row q-col-gutter-md q-pa-md">
				<div class="col-6">
					<q-btn
						color="positive"
						icon="done"
						class="q-pa-md full-height full-width"
						@click="confirm"
					/>
				</div>
				<div class="col-6">
					<q-btn
						color="red"
						icon="clear"
						class="q-pa-md full-height full-width"
						@click="cancel"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"
import { defineEmits } from "vue"

defineEmits({
	...useDialogPluginComponent.emits
})

const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

const confirm = () => {
	onDialogOK()
}
const cancel = () => {
	onDialogCancel()
}
</script>
