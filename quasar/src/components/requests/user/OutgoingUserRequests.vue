<template>
	<q-table
		grid
		:rows="user_outgoing_requests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
	>
		<template v-slot:top>
			<div class="col-xs-12 col-md-10 q-mb-sm">
				<span class="text-h6">Исходящие</span>
			</div>
			<q-btn
				:to="{ name: 'personal_user_outgoing_request'}"
				size="md"
				color="secondary"
				label="Создать"
				class="full-height col-xs-12 col-md-2"
			/>
		</template>
		<template v-slot:item="props">
			<div class="col-xs-12">
				<q-card class="q-mb-md">
					<q-card-section>
						<div class="row items-center text-center">
							<div class="col-xs-2 col-md-1">
								<q-btn
									size="md"
									color="primary"
									round
									dense
									@click="props.expand = !props.expand"
									:icon="props.expand ? 'expand_less' : 'expand_more'"
								/>
							</div>
							<div class="col-xs-7 col-md-10">
								Статус: {{ props.row.status.label }}<br/>
								Получатель: {{ props.row.to.team.display_name }}
							</div>
							<q-btn
								v-if="props.row.status.id === 1"
								icon="delete"
								size="md"
								color="warning"
								class="full-height col-xs-3 col-md-1"
								@click="cancelRequest(props.row.id)"
							/>
							<q-btn
								v-if="props.row.status.id === 4"
								icon="restore"
								size="md"
								color="secondary"
								class="full-height col-xs-3 col-md-1"
								@click="restoreRequest(props.row.id)"
							/>
						</div>
					</q-card-section>
					<q-separator v-show="props.expand" />
					<q-card-section
						v-show="props.expand"
						:props="props"
					>
						<div class="text-left">{{ props.row.message ?? "Сообщение отсутствует" }}</div>
					</q-card-section>
				</q-card>
			</div>
		</template>
		<template v-slot:no-data>
			<q-card
				square
				class="col-xs-12"
			>
				<q-card-section class="text-center">
					Заявки отсутствуют
				</q-card-section>
			</q-card>
		</template>
	</q-table>
</template>

<script>
import { useRelationRequestManager} from "src/composables/relationRequestManager"
import { useNotification } from "src/composables/notification"
export default {
	setup() {
		const
			{
				relation_request,
				user_outgoing_requests,
				userSetRequestStatus
			} = useRelationRequestManager()

		const { notifySuccess, notifyError } = useNotification()
		const cancelRequest = (request_id) => {
			userSetRequestStatus(request_id, relation_request.value.statuses.rejected_by_contributor.id)
				.then(() => {
					notifySuccess("Заявка отменена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}
		const restoreRequest = (request_id) => {
			userSetRequestStatus(request_id, relation_request.value.statuses.pending.id)
				.then(() => {
					notifySuccess("Заявка восстановлена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}

		return {
			relation_request,
			user_outgoing_requests,
			cancelRequest,
			restoreRequest
		}
	}
}
</script>