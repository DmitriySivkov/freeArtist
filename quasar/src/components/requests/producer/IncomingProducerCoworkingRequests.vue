<template>
	<q-table
		grid
		:rows="incomingCoworkingRequests(producer.id)"
		:row-key="row => row.id"
		hide-header
		hide-pagination
	>
		<template v-slot:top>
			<div class="col-xs-12 text-center">
				<span class="text-h6">Входящие заявки на вступление в команду <br/>"{{ producer.display_name }}" </span>
			</div>
		</template>
		<template v-slot:item="props">
			<q-card
				class="col-xs-12"
				square
			>
				<q-card-section class="row items-center">
					<div class="col-xs-12 col-md-8">
						Статус: {{ props.row.status.label }}<br/>
						Отправитель: {{ props.row.from.name ?? props.row.from.phone }}<br/>
						Тип: {{ relationRequest.types.coworking.label }}
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="row q-col-gutter-sm">
							<div class="col-xs-12">
								<q-btn
									label="Принять"
									size="md"
									color="secondary"
									class="full-width"
									@click="acceptCowRequest(props.row.id)"
								/>
							</div>
							<div class="col-xs-12">
								<q-btn
									label="Отказать"
									size="md"
									color="warning"
									class="full-width"
									@click="rejectCowRequest(props.row.id)"
								/>
							</div>
						</div>
					</div>

				</q-card-section>
				<q-separator />
				<q-card-section>
					<div class="row items-center text-center">
						<div class="col-xs-12">
							<q-btn
								size="md"
								color="primary"
								round
								dense
								@click="props.expand = !props.expand"
								:icon="props.expand ? 'expand_less' : 'expand_more'"
							/>
						</div>
					</div>
				</q-card-section>
				<q-card-section
					v-show="props.expand"
					:props="props"
				>
					<div class="text-left">{{ props.row.message ?? "Сообщение отсутствует" }}</div>
				</q-card-section>
			</q-card>
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
import { useRelationRequestManager } from "src/composables/relationRequestManager"
import { useNotification } from "src/composables/notification"
import { useStore } from "vuex"
import { computed } from "vue"

export default {
	props: {
		producer: {
			type: Object,
			default: () => {}
		}
	},
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const
			{
				relationRequest,
				incomingCoworkingRequests,
				acceptCoworkingRequest,
				rejectCoworkingRequest,
			} = useRelationRequestManager()
		const { notifySuccess, notifyError } = useNotification()

		//todo commit acceptCoworkingRequest
		const acceptCowRequest = (requestId) => {
			acceptCoworkingRequest(requestId)
				.then(() => { notifySuccess("Заявка принята") })
				.catch((error) => { notifyError(error.response.data) })
		}

		//todo commit rejectCoworkingRequest
		const rejectCowRequest = (requestId) => {
			rejectCoworkingRequest(requestId)
				.then(() => { notifySuccess("В заявке отказано") })
				.catch((error) => { notifyError(error.response.data) })
		}

		return {
			user,
			relationRequest,
			incomingCoworkingRequests,
			acceptCowRequest,
			rejectCowRequest
		}
	}
}
</script>
