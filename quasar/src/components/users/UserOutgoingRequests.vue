<template>
	<div class="row justify-end q-mb-lg">
		<div class="col-xs-12 col-md-5 q-pa-md">
			<q-btn
				label="Создать заявку"
				class="bg-primary text-white q-pa-md full-width"
				:loading="isLoading"
				@click="showCreateRelationRequestDialog"
			/>
		</div>
	</div>

	<q-card
		v-for="request in requests"
		:key="request.id"
		class="q-pa-md q-mb-md text-center bg-secondary text-white"
	>
		<div class="text-body1 text-uppercase">Получатель</div>
		<div class="text-body1">{{ request.to.team.display_name }}</div>

		<q-separator class="full-width q-my-sm" />

		<div class="text-body1 text-uppercase">Статус</div>
		<div class="text-body1">{{ RELATION_REQUEST_STATUS_NAMES[request.status] }}</div>

		<q-separator class="full-width q-my-sm" />

		<div class="text-body1 text-uppercase">Сообщение</div>
		<div class="text-body1">{{ request.message }}</div>

		<q-separator class="full-width q-my-sm" />

		<div
			v-if="request.status === RELATION_REQUEST_STATUSES.PENDING"
			class="row bg-secondary"
		>
			<div class="col text-right">
				<q-btn
					label="Отменить"
					class="bg-primary text-white q-pa-md"
					:loading="requestLoading === request.id"
					@click="cancelRequest(request)"
				/>
			</div>
		</div>
	</q-card>
</template>

<script setup>
// todo - infinite loader
import { ref } from "vue"
import { useNotification } from "src/composables/notification"
import { Dialog } from "quasar"
import { api } from "src/boot/axios"
import { RELATION_REQUEST_STATUSES, RELATION_REQUEST_STATUS_NAMES } from "src/const/relationRequestStatuses"
import UserCreateRelationRequestDialog from "src/components/dialogs/UserCreateRelationRequestDialog.vue"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const props = defineProps({
	requests: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits([
	"requestCreated",
	"requestCanceled"
])

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(false)

const requestLoading = ref(null)

const showCreateRelationRequestDialog = () => {
	Dialog.create({
		component: UserCreateRelationRequestDialog,
	}).onOk((relationRequest) => {
		emit("requestCreated", relationRequest)
	})
}

const cancelRequest = (request) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Отменить запрос на вступление к <span class="text-bold">${request.to.team.display_name}?</span>`,
		}
	})
		.onOk(() => {
			requestLoading.value = request.id

			const promise = api.post(`personal/users/relation-requests/${request.id}`, {
				status: RELATION_REQUEST_STATUSES.CANCELED_BY_CONTRIBUTOR,
				_method: "PUT"
			})

			promise.then((response) => {
				emit("requestCanceled", {
					requestId: response.data.id,
					status: response.data.status
				})
			})

			promise.catch((error) => {
				notifyError(error.response.data.message)
			})

			promise.finally(() => requestLoading.value = null)
		})
}
</script>
