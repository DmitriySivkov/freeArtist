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
			<div class="col-12 q-mb-md">
				<q-card v-if="!!props.row.is_creating_request">
					<q-card-section class="text-center">
						Статус: Загрузка ... <br/>
						Получатель: {{ props.row.team.label }}

						<q-inner-loading
							showing
							class="q-pl-xl"
							style="align-items: flex-start"
						>
							<q-spinner-gears
								color="primary"
								size="42px"
							/>
						</q-inner-loading>
					</q-card-section>
				</q-card>
				<q-card v-else>
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
import { useRelationRequest } from "src/composables/relationRequest"
import { useNotification } from "src/composables/notification"
import { useRelationRequestStore } from "src/stores/relation-request"
export default {
	setup() {
		const relation_request_store = useRelationRequestStore()

		const
			{
				user_outgoing_requests,
				userSetRequestStatus
			} = useRelationRequest()

		const { notifySuccess, notifyError } = useNotification()

		const cancelRequest = (request_id) => {
			userSetRequestStatus(request_id, relation_request_store.statuses.rejected_by_contributor.id)
				.then(() => {
					notifySuccess("Заявка отменена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}

		const restoreRequest = (request_id) => {
			userSetRequestStatus(request_id, relation_request_store.statuses.pending.id)
				.then(() => {
					notifySuccess("Заявка восстановлена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}

		return {
			user_outgoing_requests,
			cancelRequest,
			restoreRequest
		}
	}
}
</script>
