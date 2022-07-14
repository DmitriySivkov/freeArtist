<template>
	<div
		v-for="(producer, index) in user.data.producers"
		:key="index"
	>
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
	</div>
</template>

<script>
import { useStore } from "vuex"
import { computed } from "vue"

export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)

		return {
			user
		}
	}
}
</script>
