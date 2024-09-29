<script setup>
import { computed } from "vue"
import { useUserTeam } from "src/composables/userTeam"

const { userOwnTeam } = useUserTeam()

const items = [
	{ title: "Профиль", routeName: "personal_user" },
	{ title: "Заказы", routeName: "user_orders" },
	{
		title: "Зарегистрировать изготовителя",
		routeName: "personal_register_producer",
		conditional: !userOwnTeam.value
	},
	{ title: "Заявки", routeName: "personal_user_requests" }
]

const menu = computed(() =>
	items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
)


</script>

<template>
	<div class="row q-col-gutter-xs">
		<div
			v-for="(item, index) in menu"
			:key="index"
			class="col-xs-12 col-md-6"
		>
			<q-card class="bg-secondary text-white full-height">
				<q-item
					class="q-pa-xl full-height"
					:to="{name: item.routeName}"
				>
					<q-item-section class="text-center text-h5">
						{{ item.title }}
					</q-item-section>
				</q-item>
			</q-card>
		</div>
	</div>
</template>
