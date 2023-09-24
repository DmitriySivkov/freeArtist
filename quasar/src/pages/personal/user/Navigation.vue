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

<script setup>
import { computed } from "vue"
import { useUserTeam } from "src/composables/userTeam"

const { userOwnTeam } = useUserTeam()

const items = [
	{ title: "Профиль", link: "/personal/user" },
	{ title: "Заказы", link: "/personal/user/orders" },
	{
		title: "Зарегистрировать изготовителя",
		link: "/personal/register-producer",
		conditional: !userOwnTeam.value
	},
	{ title: "Заявки", link: "/personal/user/requests" }
]

const menu = computed(() =>
	items.filter((item) => !item.hasOwnProperty("conditional") || item.conditional === true)
)


</script>
