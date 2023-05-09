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
						:to="item.link"
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

<script>
import { computed } from "vue"
import { useUserTeam } from "src/composables/userTeam"
export default {
	setup() {
		const { user_own_team } = useUserTeam()

		const items = [
			{ title: "Профиль", link: "/personal/producer" },
			{ title: "Заказы", link: "/personal/producer/orders" },
			{
				title: "Зарегистрировать изготовителя",
				link: "/personal/register-producer",
				conditional: !user_own_team.value
			},
			{ title: "Заявки", link: "/personal/team/requests" },
			{ title: "Разрешения", link: "/personal/team/permissions" },
			{ title: "Управлять продуктами", link: "/personal/producer/products" },
			{ title: "Управлять витриной", link: "/personal/producer/storefront" }
		]

		const menu = computed(() =>
			items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
		)

		return {
			menu
		}
	}
}
</script>
