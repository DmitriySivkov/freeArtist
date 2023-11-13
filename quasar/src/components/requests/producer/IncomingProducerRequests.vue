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
						Входящие
					</span>
				</div>
			</div>
		</q-card>

		<q-linear-progress
			v-if="isMounting"
			indeterminate
			color="primary"
		/>

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
			<div class="row justify-end">
				<div class="col-xs-4 col-md-3 q-mr-sm">
					<q-btn
						v-if="
							![
								RELATION_REQUEST_STATUSES.REJECTED_BY_RECIPIENT,
								RELATION_REQUEST_STATUSES.ACCEPTED
							].includes(request.status)
						"
						label="Принять"
						class="bg-primary text-white q-pa-sm full-width"
						:loading="requestLoading === request.id"
						@click="acceptRequest({ request })"
					/>
				</div>
				<div class="col-xs-4 col-md-3">
					<q-btn
						v-if="
							![
								RELATION_REQUEST_STATUSES.REJECTED_BY_RECIPIENT,
								RELATION_REQUEST_STATUSES.ACCEPTED
							].includes(request.status)
						"
						label="Отклонить"
						class="bg-warning text-white q-pa-sm full-width"
						:loading="requestLoading === request.id"
						@click="rejectRequest({ request })"
					/>
				</div>
			</div>

			<q-separator class="q-my-md"/>

			<div class="row justify-evenly">
				<div class="col-xs-12 col-sm-11">
					<div class="row q-py-sm">
						<div class="col">Отправитель</div>
						<div class="col text-right">{{ request.from.name ?? request.from.phone }}</div>
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
// todo - pagination / filter
import { ref, onMounted } from "vue"
import { useNotification } from "src/composables/notification"
import { Dialog } from "quasar"
import { api } from "src/boot/axios"
import { RELATION_REQUEST_STATUSES, RELATION_REQUEST_STATUS_NAMES } from "src/const/relationRequestStatuses"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"
const { notifySuccess, notifyError } = useNotification()

const props = defineProps({
	team: Object
})

const isMounting = ref(true)
const isCreatingRequest = ref(false)

const requests = ref([])
const requestLoading = ref(null)

const acceptRequest = ({ request }) => {
	requestLoading.value = request.id

	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Принять запрос на вступление от <span class="text-bold">${request.from.name ?? request.from.phone }?</span>`,
			headline: "Принять"
		}
	})
		.onOk(() => {
			const promise = api.post(`personal/relationRequests/teams/${props.team.id}/accept/${request.id}`)

			promise.then(() => {
				let acceptedRequest = requests.value.find((r) => r.id === request.id)

				acceptedRequest.status = RELATION_REQUEST_STATUSES.ACCEPTED

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

const rejectRequest = ({ request }) => {
	requestLoading.value = request.id

	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: `Отклонить запрос на вступление от <span class="text-bold">${request.from.name ?? request.from.phone }?</span>`,
			headline: "Отклонить"
		}
	})
		.onOk(() => {
			const promise = api.post(`personal/relationRequests/teams/${props.team.id}/reject/${request.id}`)

			promise.then(() => {
				let rejectedRequest = requests.value.find((r) => r.id === request.id)

				rejectedRequest.status = RELATION_REQUEST_STATUSES.REJECTED_BY_RECIPIENT

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
	const promise = api.get(`personal/relationRequests/teams/${props.team.id}`)

	promise.then((response) => {
		requests.value = response.data
	})

	//todo catch

	promise.finally(() => isMounting.value = false)
})
</script>
