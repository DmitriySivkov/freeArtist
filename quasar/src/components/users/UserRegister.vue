<template>
	<q-form
		@submit="onSubmit"
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
				color="primary"
			/>
		</div>

		<div class="row">
			<div class="col q-pl-none">
				<q-btn
					label="Зарегистрироваться и войти"
					type="submit"
					color="primary"
					class="q-pa-lg full-width full-height text-body1"
					:loading="isLoading"
				/>
			</div>
		</div>
	</q-form>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useNotification } from "src/composables/notification"
import { useQuasar } from "quasar"
import { useUser } from "src/composables/user"
import { api } from "src/boot/axios"

const $q = useQuasar()
const { afterLogin } = useUser()
const $router = useRouter()
const { notifySuccess, notifyError } = useNotification()

const phone = ref(null)
const accept = ref(false)
const password = ref("")
const confirm_password = ref("")
const is_pwd = ref(true)

const isLoading = ref(false)

const onSubmit = () => {
	if (!accept.value) {
		notifyError("Необходимо принять условия пользования сервисом")
		return
	}

	isLoading.value = true

	const promise = api.post("register", {
		phone: phone.value,
		password: password.value,
		consent: accept.value,
		is_mobile: $q.platform.is.capacitor
	})

	promise.then((response) => {
		afterLogin(response)
		notifySuccess("Добро пожаловать")
		$router.push({name:"personal"})
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
