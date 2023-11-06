<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<div class="row">
				<div class="col-12">
					<span class="text-body1">Введите ваш телефон</span>
				</div>
			</div>

			<q-input
				ref="phoneRef"
				filled
				type="tel"
				v-model="phone"
				mask="8 (###) ###-##-##"
				fill-mask=""
				unmasked-value
				lazy-rules="ondemand"
				:rules="[validatePhone]"
			/>

			<div class="row">
				<div class="col-12">
					<q-btn
						label="Продолжить"
						color="primary"
						class="q-pa-lg full-width"
						@click="confirm"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"
import { ref } from "vue"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const phone = ref(null)
const phoneRef = ref(null)

const confirm = () => {
	if (!phoneRef.value.validate()) return

	onDialogOK({
		phone: `8${phone.value}`
	})
}

const validatePhone = (phone) => {
	if (!phone || `8${phone}`.length !== 11) {
		return false
	}

	return true
}
</script>
