<template>
	<div
		v-for="(producer, index) in user.data.producers"
		:key="index"
	>
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
	</div>
</template>

<script>
import { useRelationRequestManager } from "src/composables/relationRequestManager"
import { useStore } from "vuex"
import { computed } from "vue"

export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)
		const
			{
				relationRequest,
				incomingProducerPartnershipRequests,
			} = useRelationRequestManager()

		return {
			user,
			relationRequest,
			incomingProducerPartnershipRequests,
		}
	}
}
</script>
