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
						val => val.length > 6 || 'не менее 6 символов',
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
import { useQuasar } from "quasar"
import { ref } from "vue"
import { useStore } from "vuex"
import { useRouter } from "vue-router"

export default {
	setup() {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()

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
					$q.notify({
						color: "red-5",
						textColor: "white",
						icon: "warning",
						message: "Необходимо принять условия пользования сервисом"
					})
				}
				else {
					$store.dispatch("user/signUp", {
						phone: phone.value,
						password: password.value,
						consent: accept.value
					}).then(() => {
						$q.notify({
							color: "green-4",
							textColor: "white",
							icon: "cloud_done",
							message: "Добро пожаловать"
						})
						$router.push("/")
					}).catch((error) => {
						const errors = Object.values(error.response.data.errors)
							.reduce((accum, val) => accum.concat(...val), [])
						for (var val of errors) {
							$q.notify({
								color: "red-5",
								textColor: "white",
								multiline: true,
								icon: "warning",
								message: val
							})
						}
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
