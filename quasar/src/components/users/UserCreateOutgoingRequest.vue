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
						Присоединиться к существующей команде.<br/>
						Вы по-прежнему сможете зарегистрироваться собственную.
					</q-card-section>
				</q-card>

				<q-select
					filled
					v-model="team"
					use-input
					input-debounce="1000"
					label="Начните вводить название компании *"
					:options="team_list"
					@filter="loadTeamList"
					behavior="dialog"
					lazy-rules
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
import { useUserStore } from "src/stores/user"
import { useRelationRequestStore } from "src/stores/relation-request"
export default {
	setup() {
		const $router = useRouter()
		const user_store = useUserStore()
		const relation_request_store = useRelationRequestStore()
		const { notifySuccess, notifyError } = useNotification()

		const team = ref(null)
		const message = ref("")

		const team_list = ref([])

		const onSubmit = () => {
			relation_request_store.commitUserRequest({
				is_creating_request: true,
				team: team.value,
				message: message.value
			})

			const promise = user_store.createRequest({
				team: team.value,
				message: message.value
			})

			$router.push({ name: "personal_user_requests" })

			promise.then((response) => {
				relation_request_store.commitUserRequest(response.data)

				relation_request_store.removeUserCreatingRequest()

				notifySuccess("Заявка успешно отправлена")
			})

			promise.catch((error) => {
				relation_request_store.removeUserCreatingRequest()

				notifyError(error.response.data.message)
			})
		}

		const onReset = () => team.value = null

		const loadTeamList = async (query, update) => {
			if (query.length < 1) return

			const response = await api.get("personal/users/nonRelatedTeams",{
				params: { query }
			})

			update(() => {
				team_list.value = response.data.map((team) => {
					return {
						label: team.display_name,
						value: team.id
					}
				})
			})
		}

		return {
			team,
			message,
			team_list,
			onSubmit,
			onReset,
			loadTeamList
		}
	},
}
</script>
