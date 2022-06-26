<template>
	<q-table
		grid
		:rows="outgoingCoworkingRequests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
	>
		<template v-slot:top>
			<div class="col-xs-12 col-md-10 q-mb-sm">
				<span class="text-h6">Исходящие на вступление в команду </span>
			</div>
			<q-btn
				:to="{ name: 'personal_coworking_request'}"
				size="md"
				color="secondary"
				label="Создать"
				class="full-height col-xs-12 col-md-2"
			/>
		</template>
		<template v-slot:item="props">
			<div class="col-xs-12">
				<q-card class="q-ml-md q-mb-md q-mr-md">
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
								Получатель: {{ props.row.to.title }}<br/>
								Тип: {{ relationRequest.types.coworking.label }}
							</div>
							<q-btn
								v-if="props.row.status.id === 1"
								icon="delete"
								size="md"
								color="warning"
								class="full-height col-xs-3 col-md-1"
								@click="cancelCowRequest(props.row.id)"
							/>
							<q-btn
								v-if="props.row.status.id === 4"
								icon="restore"
								size="md"
								color="secondary"
								class="full-height col-xs-3 col-md-1"
								@click="restoreCowRequest(props.row.id)"
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
			<div class="col-xs-12">
				<q-card>
					<q-card-section>
						Заявки отсутствуют
					</q-card-section>
				</q-card>
			</div>
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
				relationRequest,
				outgoingCoworkingRequests,
				cancelCoworkingRequest,
				restoreCoworkingRequest
			} = useRelationRequestManager()
		const { notifySuccess, notifyError } = useNotification()
		const cancelCowRequest = (requestId) => {
			cancelCoworkingRequest(requestId)
				.then(() => {
					notifySuccess("Заявка отменена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}
		const restoreCowRequest = (requestId) => {
			restoreCoworkingRequest(requestId)
				.then(() => {
					notifySuccess("Заявка восстановлена")
				})
				.catch((error) => {
					notifyError(error)
				})
		}

		return {
			relationRequest,
			outgoingCoworkingRequests,
			cancelCowRequest,
			restoreCowRequest
		}
	}
}
</script>
