<template>
	<q-card
		v-for="request in requests"
		:key="request.id"
		class="q-pa-md q-mb-md text-center bg-secondary text-white"
	>
		<div class="text-body1 text-uppercase">Отправитель</div>
		<div class="text-body1">{{ request.from.name ?? request.from.phone }}</div>

		<q-separator class="full-width q-my-sm" />

		<div class="text-body1 text-uppercase">Статус</div>
		<div class="text-body1">{{ RELATION_REQUEST_STATUS_NAMES[request.status] }}</div>

		<q-separator class="full-width q-my-sm" />

		<div class="text-body1 text-uppercase">Сообщение</div>
		<div class="text-body1">{{ request.message }}</div>

		<q-separator class="full-width q-my-sm" />

		<div
			class="row bg-secondary justify-between q-col-gutter-sm"
			v-if="request.status === RELATION_REQUEST_STATUSES.PENDING"
		>
			<div class="col-xs-6 col-sm-4 col-lg-3">
				<q-btn
					label="Подтвердить"
					class="bg-primary text-white q-pa-md full-width"
					:loading="requestLoading === request.id"
					@click="acceptRequest(request)"
				/>
			</div>
			<div class="col-xs-6 col-sm-4 col-lg-3">
				<q-btn
					label="Отказать"
					class="bg-primary text-white q-pa-md full-width"
					:loading="requestLoading === request.id"
					@click="rejectRequest(request)"
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
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const props = defineProps({
	requests: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits([
	"requestAccepted",
	"requestRejected"
])

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(false)

const requestLoading = ref(null)

const acceptRequest = (request) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Принять пользователя <span class="text-bold">${request.from.name ?? request.from.phone}</span> в команду?`,
		}
	})
		.onOk(() => {
			requestLoading.value = request.id

			const promise = api.post(`personal/producers/relation-requests/${request.id}/accept`)

			promise.then((response) => {
				emit("requestAccepted", {
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

const rejectRequest = (request) => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Не принимать пользователя <span class="text-bold">${request.from.name ?? request.from.phone}</span> в команду?`,
		}
	})
		.onOk(() => {
			requestLoading.value = request.id

			const promise = api.post(`personal/producers/relation-requests/${request.id}/reject`)

			promise.then((response) => {
				emit("requestRejected", {
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
