<template>
	<q-page class="flex flex-center">
		<q-form
			@submit="onSubmit"
			@reset="onReset"
			class="q-gutter-md q-pa-md"
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


			<div class="row q-col-gutter-sm q-mt-none">
				<div
					class="col-6"
					:class="{'q-pl-none': item.value % 2}"
					v-for="item in firm_radio"
					:key="item.value"
				>
					<q-card
						class="bg-primary "
						dark
					>
						<q-radio
							v-model="firm_radio_model"
							:val="item.value"
							dark
							color="white"
							class="q-pt-lg q-pb-lg q-pl-sm full-width"
						>
							<template v-slot:default>
								<div class="q-pr-sm q-pl-xs">
									{{ item.label }}
								</div>
							</template>
						</q-radio>
					</q-card>
				</div>
			</div>


			<div v-if="firm_radio_model === 1">
				<div class="row">
					<q-badge
						color="secondary"
						class="col-xs-12 text-center justify-center no-border-radius"
					>
						Выберите фирму
					</q-badge>
				</div>
				<q-select
					filled
					class="q-mt-none q-pb-none"
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
			</div>
			<div v-if="firm_radio_model === 2">
				<div class="row">
					<q-badge
						color="secondary"
						class="col-xs-12 text-center justify-center no-border-radius"
					>
						Создайте свою фирму
					</q-badge>
				</div>
				<q-input
					filled
					class="q-mt-none q-pb-none"
					v-model="new_firm_title"
					label="Название фирмы *"
					:lazy-rules="true"
					:rules="[
						val => !!val || 'Введите название своей фирмы',
					]"
				/>
			</div>

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
	</q-page>
</template>

<script>
import { useQuasar } from "quasar"
import { ref, onMounted } from "vue"
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
		const is_pwd = ref(true)
		const producer = ref(null)

		const new_firm_title = ref("")

		const firm_radio_model = ref(null)
		const firm_radio = [{label:"Выбрать из списка фирм", value:1},{label:"Создать свою фирму", value:2}]

		const producerList = ref([])
		let producerListDefault = []

		onMounted(async () =>
			await api.get("producers").then((response) => {
				producerList.value = response.data.producers.map((item) => {
					return {
						label: item.title,
						value: item.id
					}
				})
				producerListDefault = producerList.value
			}))

		return {
			name,
			email,
			password,
			is_pwd,
			producer,
			producerList,
			accept,
			firm_radio,
			firm_radio_model,
			new_firm_title,

			onSubmit () {
				if (!firm_radio_model.value) {
					$q.notify({
						color: "red-5",
						textColor: "white",
						icon: "warning",
						message: "Выберите фирму или создайте собственную"
					})
					return
				}
				if (accept.value !== true) {
					$q.notify({
						color: "red-5",
						textColor: "white",
						icon: "warning",
						message: "Необходимо принять условия пользования сервисом"
					})
					return
				}
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
			},

			onReset () {
				name.value = null
				email.value = null
				password.value = null
				accept.value = false
				producer.value = null
				firm_radio_model.value = null
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
