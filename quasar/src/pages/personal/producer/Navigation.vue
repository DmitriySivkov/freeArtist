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
						:to="{name: item.routeName}"
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

<script setup>
import { computed } from "vue"

const items = [
	{ title: "Профиль", routeName: "personal_producer" },
	{ title: "Заказы", routeName: "personal_producer_orders" },
	{ title: "Заявки", routeName: "personal_team_requests" },
	{ title: "Разрешения", routeName: "personal_team_permissions" },
	{ title: "Управлять продуктами", routeName: "personal_producer_products" },
	{ title: "Способы оплаты", routeName: "personal_producer_payment_methods" }
]

const menu = computed(() =>
	items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
)
</script>
