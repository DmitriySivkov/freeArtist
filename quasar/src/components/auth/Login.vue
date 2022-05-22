<template>
	<div class="q-pa-md row justify-center">
		<div class="col-xs-12 col-md-6 col-lg-3">
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
		</div>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { useStore } from "vuex"
import { useQuasar } from "quasar"
import { ref } from "vue"

export default {
	setup() {
		const $router = useRouter()
		// const $currentRoute = useRoute()
		const $store = useStore()
		const $q = useQuasar()

		const phone = ref(null)
		const password = ref("")
		const is_pwd = ref(true)

		// if ($currentRoute.query["verify-email"] && $currentRoute.query["verify-email"] === "1")
		// 	$store.dispatch("user/verifyEmail", {
		// 		hash: $currentRoute.query["hash"],
		// 		email: $currentRoute.query["email"]
		// 	}).then(() => {
		// 		$q.notify({
		// 			color: "green-4",
		// 			textColor: "white",
		// 			icon: "cloud_done",
		// 			message: "Почта успешно верифицирована!"
		// 		})
		// 	})

		return {
			phone,
			password,
			is_pwd,

			onSubmit() {
				$store.dispatch("user/login", {
					phone: phone.value,
					password: password.value,
				}).then(() => {
					$q.notify({
						color: "green-4",
						textColor: "white",
						icon: "cloud_done",
						message: "Будь как дома, путник!"
					})
					$router.replace("personal")
				}).catch((error) => {
					const errors = Object.values(error.response.data.errors).reduce((accum, val) => accum.concat(...val), [])
					for (var val of errors) {
						$q.notify({
							color: "red-5",
							textColor: "white",
							icon: "warning",
							message: val
						})
					}
				})
			},

			onReset() {
				phone.value = null
				password.value = null
			}

		}
	}
}
</script>
