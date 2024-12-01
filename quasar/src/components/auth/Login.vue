<script setup>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { useUser } from "src/composables/user"
import { api } from "src/boot/axios"
import { Platform } from "quasar"
import { useStorage } from "src/composables/storage"

const $router = useRouter()
const { notifySuccess, notifyError } = useNotification()
const { afterLogin } = useUser()

const storage = useStorage()

const phone = ref(null)
const password = ref("")
const is_pwd = ref(true)

const isLoading = ref(false)

const onSubmit = async() => {
	isLoading.value = true

	let unauthTransactionUuids = []

	if (await storage.has("orders")) {
		unauthTransactionUuids = await storage.get("orders")
	}

	const promise = api.post("auth", {
		phone: phone.value,
		password: password.value,
		is_mobile: Platform.is.capacitor,
		unauthTransactionUuids
	})

	promise.then(async(response) => {
		await afterLogin(response)
		await $router.push({ name: "personal" })
	})

	// todo - errors refactor
	promise.catch((error) => {
		const errors = Object.values(error.response.data.errors)
			.reduce((accum, val) => accum.concat(...val), [])
		notifyError(errors)
	})

	promise.finally(() => isLoading.value = false)
}
</script>

<template>
	<q-form @submit="onSubmit">
		<q-input
			filled
			type="tel"
			v-model="phone"
			label="Телефон *"
			mask="8 (###) ###-##-##"
			fill-mask=""
			:lazy-rules="true"
			:rules="[
				val => !!val || 'Введите номер телефона'
			]"
		/>

		<q-input
			v-model="password"
			filled
			:type="is_pwd ? 'password' : 'text'"
			label="Пароль *"
			:lazy-rules="true"
			:rules="[
				val => !!val || 'Введите пароль',
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
			<div class="col q-pl-none">
				<q-btn
					label="Войти"
					type="submit"
					color="primary"
					class="q-pa-lg full-width text-body1"
					:loading="isLoading"
				/>
			</div>
		</div>
	</q-form>
	<div class="full-width">
		<div class="q-pa-md text-center text-subtitle1">Ещё нет личного кабинета? -</div>
	</div>
	<q-btn
		label="Зарегистрироваться"
		to="/register"
		color="primary"
		class="q-pa-lg full-width text-body1"
	/>
</template>
