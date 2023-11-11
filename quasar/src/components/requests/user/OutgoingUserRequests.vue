<template>
	<div
		class="column no-wrap"
		style="min-height:100vh"
	>
		<q-card
			flat
			square
			class="col-2 q-mb-xs"
		>
			<div class="row q-pa-md flex-center">
				<div class="col">
					<span class="text-body1">
						Исходящие
					</span>
				</div>
				<div class="col text-right">
					<q-btn
						label="Создать заявку"
						class="bg-primary text-white q-pa-md"
						:loading="isCreatingRequest"
						@click="showCreateRelationRequestDialog"
					/>
				</div>
			</div>
		</q-card>

		<q-card
			v-if="isCreatingRequest"
			class="col-auto q-mb-xs q-pa-md"
		>
			<div class="row q-py-sm">
				<div class="col">Статус</div>
			</div>
			<div class="row q-py-sm">
				<div class="col">Получатель</div>
			</div>
			<q-inner-loading showing>
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>

		<q-card
			v-for="request in requests"
			:key="request.id"
			class="col-auto q-mb-xs q-pa-md"
		>
			<div class="row q-mb-lg">
				<div class="col text-right">
					<q-btn
						v-if="
							![
								RELATION_REQUEST_STATUSES.REJECTED_BY_RECIPIENT,
								RELATION_REQUEST_STATUSES.ACCEPTED
							].includes(request.status)
						"
						label="Отменить"
						class="bg-primary text-white q-pa-sm"
						:loading="requestLoading === request.id"
						@click="cancelRequest({ request })"
					/>
				</div>
			</div>
			<div class="row justify-evenly">
				<div class="col-xs-12 col-sm-11">
					<div class="row q-py-sm">
						<div class="col">Получатель</div>
						<div class="col text-right">{{ request.to.team.display_name }}</div>
					</div>
					<div class="row q-py-sm">
						<div class="col">Статус</div>
						<div class="col text-right">{{ RELATION_REQUEST_STATUS_NAMES[request.status] }}</div>
					</div>
					<div class="row q-py-sm text-center">
						<div class="col-12">Сообщение</div>
						<div class="col-12">{{ request.message }}</div>
					</div>
				</div>
			</div>
			<q-inner-loading :showing="requestLoading === request.id">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useNotification } from "src/composables/notification"
import { Dialog } from "quasar"
import { api } from "src/boot/axios"
import { RELATION_REQUEST_STATUSES, RELATION_REQUEST_STATUS_NAMES } from "src/const/relationRequestStatuses"
import UserCreateRelationRequestDialog from "src/components/dialogs/UserCreateRelationRequestDialog.vue"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(true)
const isCreatingRequest = ref(false)

const requests = ref([])
const requestLoading = ref(null)

const showCreateRelationRequestDialog = () => {
	Dialog.create({
		component: UserCreateRelationRequestDialog,
	}).onOk(({teamId, message}) => {
		isCreatingRequest.value = true

		const promise = api.post("personal/relationRequests/users", {
			team_id: teamId,
			message
		})

		promise.then((response) => {
			requests.value.unshift(response.data)
			notifySuccess("Заявка отправлена")
		})

		promise.catch((error) => {
			notifyError(error.response.data.message)
		})

		promise.finally(() => {
			isCreatingRequest.value = false
		})
	})
}

const cancelRequest = ({ request }) => {
	requestLoading.value = request.id

	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Отменить запрос на вступление к <span class="text-bold">${request.to.team.display_name}?</span>`,
			headline: "Подтвердите действие"
		}
	})
		.onOk(() => {
			const promise = api.post(`personal/relationRequests/users/${request.id}`, {
				_method: "DELETE"
			})

			promise.then(() => {
				requests.value = requests.value.filter((r) => r.id !== request.id)

				notifySuccess("Успешно")
			})

			promise.catch((error) => {
				notifyError(error.response.data.message)
			})

			promise.finally(() => requestLoading.value = null)
		})
		.onCancel(() => {
			requestLoading.value = null
		})
}

onMounted(() => {
	const promise = api.get("personal/relationRequests/users")

	promise.then((response) => {
		requests.value = response.data
	})

	//todo catch

	promise.finally(() => isLoading.value = false)
})
</script>
