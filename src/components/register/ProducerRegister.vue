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
					v-model="name"
					label="Ваше имя *"
					:lazy-rules="true"
					:rules="[
						val => !!val || 'Введите имя',
						val => val.length > 1 || 'не менее 2 символов',
					]"
				/>

				<q-input
					filled
					v-model="email"
					label="Адрес электронной почты *"
					:lazy-rules="true"
					:rules="[
						val => !!val || 'Введите адрес электронной почты',
						isValidEmail
					]"
				/>

				<q-input
					v-model="password"
					filled
					:type="isPwd ? 'password' : 'text'"
					label="Пароль *"
					:lazy-rules="true"
					:rules="[
						val => !!val || 'Введите пароль',
						val => val.length > 6 || 'не менее 6 символов',
					]"
				>
					<template v-slot:append>
						<q-icon
							:name="isPwd ? 'visibility_off' : 'visibility'"
							class="cursor-pointer"
							@click="isPwd = !isPwd"
						/>
					</template>
				</q-input>

				<q-select
					filled
					v-model="producer"
					use-input
					input-debounce="0"
					label="Выбрать фирму *"
					:options="producerList"
					@filter="this.filterSelect"
					behavior="dialog"
					:rules="[
						val => !!val || 'Выберите фирму'
					]"
				>
					<template v-slot:no-option>
						<q-item>
							<q-item-section class="text-grey">
								Фирма не найдена
							</q-item-section>
						</q-item>
					</template>
				</q-select>

				<q-toggle
					v-model="accept"
					label="Я принимаю условия пользования сервисом"
				/>

				<div class="row justify-evenly">
					<q-btn
						label="Подтвердить"
						type="submit"
						color="primary"
						class="col-xs-12 col-md-5"
					/>
					<q-btn
						label="Сбросить"
						type="reset"
						color="primary"
						flat
						class="q-ml-sm col-xs-12 col-md-5"
					/>
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
import { api } from "src/boot/axios"

export default {
	setup() {
		const $q = useQuasar()
		const $store = useStore()
		const $router = useRouter()

		const name = ref(null)
		const email = ref(null)
		const accept = ref(false)
		const password = ref("")
		const isPwd = ref(true)
		const producer = ref(null)

		const producerList = ref([])
		let producerListDefault = []

		api.get("producers").then((response) => {
			producerList.value = response.data.producers.map((item) => {
				return {
					label: item.title,
					value: item.id
				}
			})
			producerListDefault = producerList.value
		})

		return {
			name,
			email,
			password,
			isPwd,
			producer,
			producerList,
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
						name: name.value,
						email: email.value,
						password: password.value,
						role_id: $router.currentRoute.value.meta.role_id,
						consent: accept.value
					}).then(() => {
						$q.notify({color: "green-4", textColor: "white",
							icon: "cloud_done",
							message: "На указанную почту отправлено письмо для подтверждения"
						})
					}).catch((error) => {
						const errors = Object.values(error).reduce((accum, val) => accum.concat(...val), [])
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
				name.value = null
				email.value = null
				password.value = null
				accept.value = false
				producer.value = null
			},

			isValidEmail (val) {
				return /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/.test(val)
            || "Неверный формат"
			},

			filterSelect (val, update) {
				if (val === "") {
					update(() => producerList.value = producerListDefault)
					return
				}
				update(() => {
					producerList.value = producerList.value.filter(v => v.label.toLowerCase().indexOf(val.toLowerCase()) > -1)
				})
			}

		}
	},
}
</script>
