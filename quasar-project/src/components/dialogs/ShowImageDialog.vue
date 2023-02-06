<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:position="$q.platform.is.mobile ? 'bottom' : 'standard'"
	>
		<q-card class="q-dialog-plugin">
			<div class="row q-pa-md">
				<div class="col-12">
					<q-img
						:src="imagePath"
						fit="contain"
					/>
				</div>
			</div>
			<div class="row q-col-gutter-md q-pa-md">
				<div class="col-6">
					<q-btn
						color="secondary"
						icon="done"
						class="q-pa-lg full-height full-width"
						@click="confirmPicture"
					/>
				</div>
				<div class="col-6">
					<q-btn
						color="red"
						icon="clear"
						class="q-pa-lg full-height full-width"
						@click="cancelPicture"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script>
import { useDialogPluginComponent } from "quasar"

export default {
	props: {
		imagePath: String
	},

	emits: [
		...useDialogPluginComponent.emits,
	],

	setup (props, {emit}) {
		const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()

		const confirmPicture = () => {
			onDialogOK()
		}
		const cancelPicture = () => {
			onDialogCancel()
		}
		return {
			// This is REQUIRED;
			// Need to inject these (from useDialogPluginComponent() call)
			// into the vue scope for the vue html template
			dialogRef,
			onDialogHide,
			confirmPicture,
			cancelPicture
		}
	}
}
</script>
