<template>
	<div class="q-pa-md row justify-center">
		<div class="col-xs-12 col-md-6 col-lg-4">
			<q-form
				@submit="onSubmit"
				@reset="onReset"
			>
				<q-card class="q-mb-lg">
					<q-card-section
						class="text-center bg-primary text-white"
					>
						Присоединиться к существующему изготовителю.<br/>
						Вы по-прежнему сможете зарегистрироваться как самостоятельный изготовитель
					</q-card-section>
				</q-card>

				<q-select
					filled
					v-model="producer"
					use-input
					input-debounce="1000"
					label="Начните вводить название изготовителя *"
					:options="producerList"
					@filter="loadProducerList"
					behavior="dialog"
					:rules="[
						val => !!val,
					]"
				/>

				<q-input
					v-model="message"
					filled
					type="textarea"
					label="Добавьте текст заявки (необязательно)"
				/>

				<div class="row q-col-gutter-sm q-mt-lg">
					<div class="col-xs-6">
						<q-btn
							label="Подтвердить"
							type="submit"
							color="primary"
							class="q-pa-lg full-width"
						/>
					</div>
					<div class="col-xs-6">
						<q-btn
							label="Сбросить"
							type="reset"
							color="warning"
							class="q-pa-lg full-width"
						/>
					</div>
				</div>
			</q-form>
		</div>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { ref } from "vue"
import { useNotification } from "src/composables/notification"
import { api } from "src/boot/axios"
import { useRelationRequestManager } from "src/composables/relationRequestManager"

export default {
	setup() {
		const $router = useRouter()
		const { notifySuccess, notifyError } = useNotification()
		const { sendCoworkingRequest } = useRelationRequestManager()

		const producer = ref(null)
		const message = ref("")

		const producerList = ref([])

		const onSubmit = () => {
			sendCoworkingRequest(producer.value.value, message.value)
				.then(() => {
					notifySuccess("Заявка успешно отправлена")
					$router.push({name: "personal_user_requests"})
				})
				.catch((error) => {
					const errors = Object.values(error.response.data.errors)
						.reduce((accum, val) => accum.concat(...val), [])
					notifyError(errors)
				})
		}

		const onReset = () =>
			producer.value = null

		const loadProducerList = async (query, update) => {
			if (query.length < 1) return

			const response = await api.get("personal/producers/producersToAttach",{
				params: { query }
			})

			update(() => {
				producerList.value = response.data.map((producer) => {
					return {
						label: producer.title,
						value: producer.id
					}
				})
			})
		}

		return {
			producer,
			message,
			producerList,
			onSubmit,
			onReset,
			loadProducerList
		}
	},
}
</script>
