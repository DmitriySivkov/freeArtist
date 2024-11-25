<script setup>
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { useUser } from "src/composables/user.js"
import { api } from "src/boot/axios.js"
import { Dialog } from "quasar"
import { useRouter } from "vue-router"
import CommonConfirmationDialog from "src/components/dialogs/CommonConfirmationDialog.vue"

const $router = useRouter()

const userStore = useUserStore()

const userOwnTeam = computed(() =>
	userStore.teams.find((t) => t.user_id === userStore.data.id)
)

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

const { afterLogout } = useUser()

const logout = () => {
	Dialog.create({
		component: CommonConfirmationDialog,
		componentProps: {
			text: "Подтвердите выход"
		}
	}).onOk(() => {
		api.post("personal/logout")

		afterLogout()

		$router.push({name: "home"})
	})
}
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

		<div class="col-xs-12 col-md-6">
			<q-card
				class="bg-secondary text-white full-height"
				@click="logout"
			>
				<q-item class="q-pa-xl full-height">
					<q-item-section class="text-center text-h5">
						Выйти
					</q-item-section>
				</q-item>
			</q-card>
		</div>
	</div>
</template>
