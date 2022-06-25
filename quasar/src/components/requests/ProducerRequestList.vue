<template>
	<div
		v-for="(producer, index) in user.data.producers"
		:key="index"
	>
		<q-table
			grid
			:rows="outgoingProducerPartnershipRequests(producer.id)"
			:row-key="row => row.id"
			hide-header
			hide-pagination
			:title="'Исходящие на партнерство с изготовителем ' + '&quot' + producer.title + '&quot' "
		>
			<template v-slot:item="props">
				<div class="q-pa-xs col-xs-12">
					<q-card>
						<q-card-section>
							<div class="row items-center justify-between text-center">
								<div class="col-xs-auto">
									<q-btn
										size="md"
										color="primary"
										round
										dense
										@click="props.expand = !props.expand"
										:icon="props.expand ? 'expand_less' : 'expand_more'"
									/>
								</div>
								<div class="col-xs-7 col-md-5">
									Статус: {{ props.row.status.label }}<br/>
									Получатель: {{ props.row.to.title }}<br/>
									Тип: {{ relationRequest.types.producer_partnership.label }}
								</div>
								<div class="col-xs-3 col-md-5">
								</div>
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
				Заявки отсутствуют
			</template>
		</q-table>
		<q-table
			grid
			:rows="incomingProducerPartnershipRequests(producer.id)"
			:row-key="row => row.id"
			hide-header
			hide-pagination
			:title="'Входящие на партнерство с изготовителем ' + '&quot' + producer.title + '&quot'"
		>
			<template v-slot:item="props">
				<div class="q-pa-xs col-xs-12">
					<q-card>
						<q-card-section>
							<div class="row items-center justify-between text-center">
								<div class="col-xs-auto">
									<q-btn
										size="md"
										color="primary"
										round
										dense
										@click="props.expand = !props.expand"
										:icon="props.expand ? 'expand_less' : 'expand_more'"
									/>
								</div>
								<div class="col-xs-7 col-md-5">
									Статус: {{ props.row.status.label }}<br/>
									Отправитель: {{ props.row.from.title }}<br/>
									Тип: {{ relationRequest.types.producer_partnership.label }}
								</div>
								<div class="col-xs-3 col-md-5">
								</div>
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
				Заявки отсутствуют
			</template>
		</q-table>
		<!--		<q-table-->
		<!--			grid-->
		<!--			:rows="requests"-->
		<!--			:row-key="row => row.id"-->
		<!--			hide-header-->
		<!--			hide-pagination-->
		<!--			title="Исходящие приглашения в команду"-->
		<!--		>-->
		<!--			<template v-slot:item="props">-->
		<!--				<div class="q-pa-xs col-xs-12">-->
		<!--					<q-card>-->
		<!--						<q-card-section>-->
		<!--							<div class="row items-center justify-between text-center">-->
		<!--								<div class="col-xs-auto">-->
		<!--									<q-btn-->
		<!--										size="md"-->
		<!--										color="primary"-->
		<!--										round-->
		<!--										dense-->
		<!--										@click="props.expand = !props.expand"-->
		<!--										:icon="props.expand ? 'expand_less' : 'expand_more'"-->
		<!--									/>-->
		<!--								</div>-->
		<!--								<div class="col-xs-7 col-md-5">-->
		<!--									to: {{ props.row.to }}-->
		<!--									status: {{ props.row.status }}-->
		<!--								</div>-->
		<!--								<div class="col-xs-3 col-md-5">-->
		<!--									<div class="row justify-center">-->
		<!--										<q-btn-->
		<!--											icon="block"-->
		<!--											size="md"-->
		<!--											color="warning"-->
		<!--											class="full-height q-ma-xs col-xs-12 col-md-2"-->
		<!--										/>-->
		<!--									</div>-->
		<!--								</div>-->
		<!--							</div>-->
		<!--						</q-card-section>-->
		<!--						<q-separator v-show="props.expand" />-->
		<!--						<q-card-section-->
		<!--							v-show="props.expand"-->
		<!--							:props="props"-->
		<!--						>-->
		<!--							<div class="text-left">{{ props.row.message ?? "Сообщение отсутствует" }}</div>-->
		<!--						</q-card-section>-->
		<!--					</q-card>-->
		<!--				</div>-->
		<!--			</template>-->
		<!--			<template v-slot:no-data>-->
		<!--				Заявки отсутствуют-->
		<!--			</template>-->
		<!--		</q-table>-->
		<q-table
			grid
			:rows="incomingCoworkingRequests(producer.id)"
			:row-key="row => row.id"
			hide-header
			hide-pagination
			:title="'Входящие заявки на вступление в команду ' + '&quot' + producer.title + '&quot'"
		>
			<template v-slot:item="props">
				<div class="q-pa-xs col-xs-12">
					<q-card>
						<q-card-section>
							<div class="row items-center justify-between text-center">
								<div class="col-xs-auto">
									<q-btn
										size="md"
										color="primary"
										round
										dense
										@click="props.expand = !props.expand"
										:icon="props.expand ? 'expand_less' : 'expand_more'"
									/>
								</div>
								<div class="col-xs-7 col-md-5">
									Статус: {{ props.row.status.label }}<br/>
									Отправитель: {{ props.row.from.name }}<br/>
									Тип: {{ relationRequest.types.coworking.label }}
								</div>
								<div class="col-xs-3 col-md-5">
								</div>
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
				Заявки отсутствуют
			</template>
		</q-table>
	</div>
</template>

<script>
import { useRelationRequestManager } from "src/composables/relationRequestManager"
import { useNotification } from "src/composables/notification"
import { useStore } from "vuex"
import { computed } from "vue"

export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const
			{
				relationRequest,
				outgoingProducerPartnershipRequests,
				incomingProducerPartnershipRequests,
				incomingCoworkingRequests,
			} = useRelationRequestManager()
		const { notifySuccess, notifyError } = useNotification()

		return {
			user,
			relationRequest,
			outgoingProducerPartnershipRequests,
			incomingProducerPartnershipRequests,
			incomingCoworkingRequests,
		}
	}
}
</script>
