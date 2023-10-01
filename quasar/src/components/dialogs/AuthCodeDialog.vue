<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">
			<q-input
				filled
				type="number"
				v-model.number="code"
				label="Введите код"
				mask="####"
				:lazy-rules="true"
				:rules="[
					val => !!val
				]"
			/>

			<div class="row">
				<div class="col-12">
					<q-btn
						label="Подтвердить"
						color="primary"
						class="q-pa-lg full-width"
						:loading="isLoading"
						@click="sendCode"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { useQuasar } from "quasar"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"

const props = defineProps({
	phone: String,
	isAuth: Boolean
})

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const $q = useQuasar()
const userStore = useUserStore()

const { notifyError } = useNotification()
const code = ref(null)

const isLoading = ref(false)

const sendCode = () => {
	if (!code.value) return

	isLoading.value = true

	const promise = api.post("auth", {
		phone: props.phone,
		code: code.value,
		is_auth: props.isAuth,
		is_mobile: $q.platform.is.capacitor
	})

	promise.then((response) => {
		onDialogOK(response)
	})

	promise.catch((error) => {
		notifyError(error.response.data.message)
	})

	promise.finally(() => isLoading.value = false)
}
</script>
