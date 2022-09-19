<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin">
			<q-card-section
				horizontal
				class="q-col-gutter-sm q-pa-md"
			>
				<div class="col-6">
					<q-btn
						color="primary"
						label="Открыть галерею"
						class="q-pa-lg full-width"
						@click="test(1)"
					/>
				</div>
				<div class="col-6">
					<q-btn
						color="primary"
						label="Сделать снимок"
						class="q-pa-lg full-width"
						@click="test(2)"
					/>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>

<script>
import { useDialogPluginComponent } from "quasar"

export default {
	props: {},

	emits: [
		...useDialogPluginComponent.emits,
		"zxc"
	],

	setup (props, {emit}) {
		const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
		// abuse "onOk" event
		const test = (option) => {
			onDialogOK(option)
		}
		return {
			// This is REQUIRED;
			// Need to inject these (from useDialogPluginComponent() call)
			// into the vue scope for the vue html template
			dialogRef,
			onDialogHide,
			test
		}
	}
}
</script>
