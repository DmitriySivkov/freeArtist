<template>
	<q-page class="flex flex-center">
		<div class="q-pa-md">
			<q-form
				@submit="onSubmit"
				@reset="onReset"
				class="q-gutter-md"
			>

				<q-input
					filled
					v-model="phone"
					type="tel"
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

				<q-input
					v-model="confirm_password"
					filled
					type="password"
					label="Введите пароль ещё раз *"
					:lazy-rules="true"
					:rules="[
						val => val === password || 'Пароли не совпадают',
					]"
				/>

				<div class="row q-mt-none">
					<q-toggle
						v-model="accept"
						label="Я принимаю условия пользования сервисом"
						size="lg"
						class="col-xs-12 q-mt-md"
						color="secondary"
					/>
				</div>

				<div class="row q-col-gutter-sm">
					<div class="col-6 q-pl-none">
						<q-btn
							label="Зарегистрироваться и войти"
							type="submit"
							color="primary"
							class="q-pa-lg full-width full-height"
						/>
					</div>
					<div class="col-6 q-pr-none">
						<q-btn
							label="Сбросить"
							type="reset"
							color="warning"
							class="q-pa-lg full-width full-height"
						/>
					</div>
				</div>
			</q-form>

		</div>
	</q-page>
</template>

<script>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useQuasar } from "quasar"
import { Plugins } from "@capacitor/core"
import { useUserStore } from "src/stores/user"
export default {
	setup() {
		const $q = useQuasar()
		const user_store = useUserStore()
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()
		const { Storage } = Plugins

		const phone = ref(null)
		const accept = ref(false)
		const password = ref("")
		const confirm_password = ref("")
		const is_pwd = ref(true)

		return {
			phone,
			password,
			confirm_password,
			is_pwd,
			accept,

			onSubmit () {
				if (accept.value !== true) {
					notifyError("Необходимо принять условия пользования сервисом")
				} else {
					user_store.signUp({
						phone: phone.value,
						password: password.value,
						consent: accept.value,
						is_mobile: $q.platform.is.capacitor
					}).then((response) => {
						if (response.data.token) {
							Storage.set({
								key: "token",
								value: response.data.token
							})
						}
						notifySuccess("Добро пожаловать")
						$router.push({name:"personal"})
					}).catch((error) => {
						const errors = Object.values(error.response.data.errors)
							.reduce((accum, val) => accum.concat(...val), [])
						notifyError(errors)
					})
				}
			},

			onReset () {
				phone.value = null
				password.value = null
				confirm_password.value = null
				accept.value = false
			},

		}
	},
}
</script>
