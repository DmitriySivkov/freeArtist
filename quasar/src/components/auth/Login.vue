<template>
	<q-form
		@submit="onSubmit"
		@reset="onReset"
		class="q-gutter-md"
	>
		<q-input
			filled
			type="tel"
			v-model="phone"
			label="Телефон *"
			mask="# (###) ###-##-##"
			:unmasked-value="true"
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

		<div class="row q-col-gutter-sm">
			<div class="col-6 q-pl-none">
				<q-btn
					label="Подтвердить"
					type="submit"
					color="primary"
					class="q-pa-lg full-width"
				/>
			</div>
			<div class="col-6 q-pr-none">
				<q-btn
					label="Сбросить"
					type="reset"
					color="warning"
					class="q-pa-lg full-width"
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
		class="q-pa-lg full-width"
	/>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { ref } from "vue"
import { Plugins } from "@capacitor/core"
import { useQuasar, Loading } from "quasar"
import { useUserStore } from "src/stores/user"

const $q = useQuasar()
const $router = useRouter()
const user_store = useUserStore()
const { Storage } = Plugins

const { notifySuccess, notifyError } = useNotification()
const phone = ref(null)
const password = ref("")
const is_pwd = ref(true)

const onSubmit = async() => {
	Loading.show({ spinnerColor: "primary" })

	await user_store.login({
		phone: phone.value,
		password: password.value,
		is_mobile: $q.platform.is.capacitor
	}).then((response) => {
		if (response.data.token) {
			Storage.set({
				key: "token",
				value: response.data.token
			})
		}

		notifySuccess("Добро пожаловать!")

		$router.push({name: "personal"})

		user_store.setIsLogged(true)
	}).catch((error) => {
		const errors = Object.values(error.response.data.errors)
			.reduce((accum, val) => accum.concat(...val), [])
		notifyError(errors)
	})

	Loading.hide()
}

const onReset = () => {
	phone.value = null
	password.value = null
}
</script>
