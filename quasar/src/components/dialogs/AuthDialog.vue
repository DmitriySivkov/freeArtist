<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin q-pa-md">

			<q-input
				v-if="isUserFound === null"
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

			<q-input
				v-else
				filled
				class="q-mb-xs"
				:type="is_pwd ? 'password' : 'text'"
				v-model="password"
				label="Пароль"
				:lazy-rules="true"
				:rules="[
					val => val.length >= 6 || 'не менее 6 символов',
				]"
			>
				<template v-slot:append>
					<q-icon
						:name="is_pwd ? 'visibility_off' : 'visibility'"
						class="cursor-pointer"
						@click="is_pwd = !is_pwd"
					/>
				</template>
			</q-input>

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
import { useDialogPluginComponent } from "quasar"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { Plugins } from "@capacitor/core"
import { useQuasar } from "quasar"
import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const $q = useQuasar()
const $router = useRouter()
const userStore = useUserStore()
const { Storage } = Plugins

const { notifySuccess, notifyError } = useNotification()
const phone = ref(null)
const password = ref("")
const is_pwd = ref(true)

const isLoading = ref(false)

const isUserFound = ref(null)

const auth = async() => {
	isLoading.value = true

	const promise = api.post("/auth/checkByPhone", {
		phone: phone.value,
		// is_mobile: $q.platform.is.capacitor
	})

	promise.then((response) => {
		isUserFound.value = response.data
	})

	promise.finally(() => isLoading.value = false)

	// if (!response.data.errors) {
	// 	if (response.data.token) {
	// 		await Storage.set({
	// 			key: "token",
	// 			value: response.data.token
	// 		})
	// 	}
	//
	// 	userStore.setIsLogged(true)
	// } else {
	// 	const errors = Object.values(error.response.data.errors)
	// 		.reduce((accum, val) => accum.concat(...val), [])
	//
	// 	notifyError(errors)
	// }
}
</script>
