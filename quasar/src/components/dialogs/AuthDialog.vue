<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">

			<q-input
				filled
				type="tel"
				v-model="phone"
				label="Телефон"
				mask="8 (###) ###-##-##"
				:unmasked-value="true"
				:lazy-rules="true"
				:rules="[
					val => !!val
				]"
			/>

			<div class="row">
				<div class="col-12">
					<q-btn
						label="Продолжить"
						color="primary"
						class="q-pa-lg full-width"
						:loading="isLoading"
						@click="auth"
					/>
				</div>
			</div>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { Dialog, useDialogPluginComponent } from "quasar"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { Plugins } from "@capacitor/core"
import { useQuasar } from "quasar"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"
import { useUser } from "src/composables/user"
import AuthCodeDialog from "components/dialogs/AuthCodeDialog.vue"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const $q = useQuasar()
const userStore = useUserStore()

const isLoading = ref(false)

const { Storage } = Plugins

const { notifySuccess, notifyError } = useNotification()
const phone = ref(null)

const { afterLogin } = useUser()

const auth = () => {
	if (!phone.value) return

	isLoading.value = true

	const promise = api.post("auth", {
		phone: `8${phone.value}`,
		is_mobile: $q.platform.is.capacitor
	})

	promise.then((response) => {
		Dialog.create({
			component: AuthCodeDialog,
			componentProps: {
				phone: `8${phone.value}`,
				isAuth: response.data.user_exists,
			}
		}).onOk((response) => {
			afterLogin(response)

			if (response.data.token) {
				Storage.set({
					key: "token",
					value: response.data.token
				})
			}

			userStore.setIsLogged(true)

			onDialogOK()
		})
	})

	promise.catch((error) => {
		notifyError(error.response.data.message)
	})

	promise.finally(() => isLoading.value = false)
}
</script>
