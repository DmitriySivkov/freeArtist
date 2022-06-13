<template>
	<q-table
		grid
		:rows="requests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
		title="Исходящие на партнерство с изготовителем"
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
								to: {{ props.row.to }}
								status: {{ props.row.status }}
							</div>
							<div class="col-xs-3 col-md-5">
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
			Заявки отсутствуют
		</template>
	</q-table>
	<q-table
		grid
		:rows="requests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
		title="Входящие на партнерство с изготовителем"
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
								to: {{ props.row.to }}
								status: {{ props.row.status }}
							</div>
							<div class="col-xs-3 col-md-5">
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
			Заявки отсутствуют
		</template>
	</q-table>
	<q-table
		grid
		:rows="requests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
		title="Исходящие на приглашение пользователя в команду"
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
								to: {{ props.row.to }}
								status: {{ props.row.status }}
							</div>
							<div class="col-xs-3 col-md-5">
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
			Заявки отсутствуют
		</template>
	</q-table>
	<q-table
		grid
		:rows="requests"
		:row-key="row => row.id"
		hide-header
		hide-pagination
		title="Входящие от пользователей на вступление в команду"
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
								to: {{ props.row.to }}
								status: {{ props.row.status }}
							</div>
							<div class="col-xs-3 col-md-5">
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
			Заявки отсутствуют
		</template>
	</q-table>
</template>

<script>
import { computed } from "vue"
import { useStore } from "vuex"

export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const requests = user.value.data.outgoing_join_requests
		return {
			requests
		}
	}
}
</script>
