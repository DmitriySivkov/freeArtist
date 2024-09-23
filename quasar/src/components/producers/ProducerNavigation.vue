<script setup>
import { computed } from "vue"

const items = [
	{ title: "Профиль", navigationRouteName: "personal_producer_profile" },
	{ title: "Заказы", navigationRouteName: "personal_producer_orders" },
	{ title: "Заявки", navigationRouteName: "personal_team_requests" },
	{ title: "Разрешения", navigationRouteName: "personal_team_permissions" },
	{ title: "Управлять продуктами", navigationRouteName: "personal_producer_products" },
	{ title: "Способы оплаты", navigationRouteName: "personal_producer_payment_methods" }
]

const menu = computed(() =>
	items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
)
</script>

<template>
	<div class="q-ma-sm">
		<div class="row q-col-gutter-sm">
			<div
				v-for="(item, index) in menu"
				:key="index"
				class="col-xs-12 col-md-6 col-lg-3 flex"
			>
				<q-card
					class="bg-indigo-10 text-white fit"
				>
					<q-item
						class="q-pa-xl fit"
						:to="{
							name: 'personal_teams',
							params: { navigationRouteName: item.navigationRouteName }
						}"
					>
						<q-item-section class="text-center text-h5">
							{{ item.title }}
						</q-item-section>
					</q-item>
				</q-card>
			</div>
		</div>
	</div>
</template>
