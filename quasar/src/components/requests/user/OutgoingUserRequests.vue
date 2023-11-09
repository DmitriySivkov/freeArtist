<template>
	<div class="column no-wrap">
		<q-card
			flat
			square
			class="col q-mb-xs"
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
						class="bg-primary text-white"
						:loading="isLoading"
						@click="showCreateRelationRequestDialog"
					/>
				</div>
			</div>
		</q-card>
		<q-card
			class="col q-mb-xs q-pa-md"
		>
			<div class="row q-py-sm">
				<div class="col">Статус</div>
				<div class="col">здесь какой-то статус</div>
			</div>
			<div class="row q-py-sm">
				<div class="col">Получатель</div>
				<div class="col">здесь какой-то получатель</div>
			</div>
			<div class="row q-py-sm text-center">
				<div class="col-12">Сообщение</div>
				<div class="col-12">здесь какое-то сообщение</div>
			</div>
		</q-card>
	</div>
</template>

<script setup>
import { ref } from "vue"
import { useNotification } from "src/composables/notification"
import { Dialog } from "quasar"
import UserCreateRelationRequestDialog from "src/components/dialogs/UserCreateRelationRequestDialog.vue"
import {api} from "boot/axios"

const { notifySuccess, notifyError } = useNotification()

const isLoading = ref(false)

const showCreateRelationRequestDialog = () => {
	Dialog.create({
		component: UserCreateRelationRequestDialog,
	}).onOk(({teamId, message}) => {
		isLoading.value = true

		const promise = api.post("personal/relationRequests/users", {
			team_id: teamId,
			message
		})

		promise.then((response) => {
			notifySuccess("Заявка успешно отправлена")
		})

		promise.catch((error) => {
			notifyError(error.response.data.message)
		})

		promise.finally(() => {
			isLoading.value = false
		})
	})
}

const cancelRequest = (requestId) => {

}

const restoreRequest = (requestId) => {

}
</script>
