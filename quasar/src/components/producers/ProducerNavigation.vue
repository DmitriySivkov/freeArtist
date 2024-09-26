<script setup>
import { computed } from "vue"
import { TEAM_ENTITIES } from "src/const/teamEntities"

const items = [
	{ title: "Профиль", navigationRouteName: "personal_producer_profile" },
	{ title: "Заказы", navigationRouteName: "personal_producer_orders" },
	{ title: "Заявки", navigationRouteName: "personal_producer_requests" },
	{ title: "Разрешения", navigationRouteName: "personal_producer_permissions" },
	{ title: "Управлять продуктами", navigationRouteName: "personal_producer_products" },
	{ title: "Способы оплаты", navigationRouteName: "personal_producer_payment_methods" }
]

const menu = computed(() =>
	items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
)
</script>

<template>
	<div class="row q-col-gutter-sm q-pa-xs">
		<div
			v-for="(item, index) in menu"
			:key="index"
			class="col-xs-12 col-md-6 col-lg-3"
		>
			<q-card class="bg-secondary text-white">
				<q-item
					class="q-pa-xl"
					:to="{
						name: 'personal_teams',
						params: {
							entity: TEAM_ENTITIES.PRODUCER,
							navigationRouteName: item.navigationRouteName
						}
					}"
				>
					<q-item-section class="text-center text-h5">
						{{ item.title }}
					</q-item-section>
				</q-item>
			</q-card>
		</div>
	</div>
</template>
