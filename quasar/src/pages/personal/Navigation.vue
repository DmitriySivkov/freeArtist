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
import { useStore } from "vuex"
export default {
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user.data)
		const producer_user_rights = computed(() => $store.state.producer.user_rights)
		const items = [
			{ title: "Персональные данные", link: "/personal/user" },
			{ title: "Ваши заказы", link: "/personal/orders" },
			{
				title: "Зарегистрировать изготовителя",
				link: "/personal/register-producer",
				conditional: !user.value.producers.find(
					(producer) => producer.pivot.user_id === user.value.id &&
						producer.pivot.rights.includes(producer_user_rights.value.owner)
				)
			},
			{ title: "Присоединиться к изготовителю", link: "/personal/join-producer" }
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
