<template>
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
				label="Подтвердить"
				color="primary"
				class="q-pa-lg full-width"
				@click="auth"
			/>
		</div>
	</div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { Plugins } from "@capacitor/core"
import { useQuasar } from "quasar"
import { useUserStore } from "src/stores/user"
import { api } from "boot/axios"
import { useUser } from "src/composables/user"

const $q = useQuasar()
const $router = useRouter()
const userStore = useUserStore()

const { Storage } = Plugins

const { notifySuccess, notifyError } = useNotification()
const phone = ref(null)

const { afterLogin } = useUser()

const auth = () => {
	const promise = api.post("auth", {
		phone: phone.value,
		is_mobile: $q.platform.is.capacitor
	})

	promise.then((response) => {
		afterLogin(response)

		if (response.data.token) {
			Storage.set({
				key: "token",
				value: response.data.token
			})
		}

		$router.push({name: "personal"})

		userStore.setIsLogged(true)
	})

	promise.catch((error) => {
		const errors = Object.values(error.response.data.errors)
			.reduce((accum, val) => accum.concat(...val), [])
		notifyError(errors)
	})
}
</script>
