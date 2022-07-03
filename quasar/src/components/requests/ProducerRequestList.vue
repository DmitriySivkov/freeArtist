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
		>
			<template v-slot:top>
				<div class="col-xs-12 col-md-10 q-mb-sm">
					<span class="text-h6">Исходящие на партнерство с изготовителем "{{producer.title}}" </span>
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
									Тип: {{ relationRequest.types.producer_partnership.label }}
								</div>
								<div class="col-xs-3 col-md-1">
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
				<div class="col-xs-12 col-lg-4">
					<q-card>
						<q-card-section>
							Заявки отсутствуют
						</q-card-section>
					</q-card>
				</div>
			</template>
		</q-table>
		<q-table
			grid
			:rows="incomingProducerPartnershipRequests(producer.id)"
			:row-key="row => row.id"
			hide-header
			hide-pagination
		>
			<template v-slot:top>
				<div class="col-xs-12 col-md-10 q-mb-sm">
					<span class="text-h6">Входящие на партнерство с изготовителем "{{producer.title}}" </span>
				</div>
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
									Отправитель: {{ props.row.from.title }}<br/>
									Тип: {{ relationRequest.types.producer_partnership.label }}
								</div>
								<div class="col-xs-3 col-md-1">
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
				<div class="col-xs-12 col-lg-4">
					<q-card>
						<q-card-section>
							Заявки отсутствуют
						</q-card-section>
					</q-card>
				</div>
			</template>
		</q-table>
		<q-table
			grid
			:rows="[]"
			:row-key="row => row.id"
			hide-header
			hide-pagination
		>
			<template v-slot:top>
				<div class="col-xs-12 col-md-10 q-mb-sm">
					<span class="text-h6">Исходящие приглашения в команду "{{ producer.title }}" </span>
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
									to: {{ props.row.to }}
									status: {{ props.row.status }}
								</div>
								<div class="col-xs-3 col-md-1">
									<div class="row justify-center">
										<q-btn
											icon="block"
											size="md"
											color="warning"
											class="full-height q-ma-xs col-xs-12 col-md-2"
										/>
									</div>
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
				<q-card class="col-xs-12 col-lg-4">
					<q-card-section>
						Заявки отсутствуют
					</q-card-section>
				</q-card>

			</template>
		</q-table>
		<q-table
			grid
			:rows="incomingCoworkingRequests(producer.id)"
			:row-key="row => row.id"
			hide-header
			hide-pagination
			:title="'Входящие заявки на вступление в команду ' + '&quot' + producer.title + '&quot'"
		>
			<template v-slot:top>
				<div class="col-xs-12 col-lg-4 text-center">
					<span class="text-h6">Входящие заявки на вступление в команду <br/>"{{ producer.title }}" </span>
				</div>
			</template>
			<template v-slot:item="props">
				<q-card
					class="col-xs-12 col-lg-4"
					square
				>
					<q-card-section class="row items-center">
						<div class="col-xs-12 col-md-8">
							Статус: {{ props.row.status.label }}<br/>
							Отправитель: {{ props.row.from.name }}<br/>
							Тип: {{ relationRequest.types.coworking.label }}
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="row q-gutter-sm">
								<q-btn
									label="Принять"
									size="md"
									color="secondary"
									class="full-height col-xs-12"
									@click="restoreCowRequest(props.row.id)"
								/>
								<q-btn
									label="Отказать"
									size="md"
									color="warning"
									class="full-height col-xs-12"
									@click="cancelCowRequest(props.row.id)"
								/>
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
				<div class="col-xs-12 col-lg-4">
					<q-card>
						<q-card-section>
							Заявки отсутствуют
						</q-card-section>
					</q-card>
				</div>
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
