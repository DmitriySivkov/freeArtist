<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<div class="text-right">
				<q-icon
					v-close-popup
					name="close"
					size="md"
					class="cursor-pointer"
				/>
			</div>

			<q-card-section
				v-if="headline"
				class="q-pa-none q-mb-md"
			>
				<div class="text-h5">{{ headline }}</div>
			</q-card-section>

			<q-card-section
				v-if="text"
				class="q-pa-none text-h6"
			>
				<div v-html="text"></div>
			</q-card-section>

			<div class="row q-mt-md">
				<div class="col-12">
					<q-btn
						label="Подтвердить"
						color="primary"
						class="q-pa-md full-width text-body1"
						@click="onDialogOK"
					/>
				</div>
			</div>

		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"

defineEmits([
	...useDialogPluginComponent.emits,
])

defineProps({
	headline: {
		type: String,
		required: false
	},
	text: {
		type: String,
		required: false
	}
})

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>
