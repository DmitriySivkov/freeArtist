<template>
	<div class="absolute full-width full-height sticky__common_top">
		<ProducerProductList
			:products="team.products"
			:is-able-to-manage-product="is_able_to_manage_product"
		/>
	</div>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import { useRouter } from "vue-router"
import { computed, ref } from "vue"
import { Loading } from "quasar"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { useTeamStore } from "src/stores/team"
import { useNotification } from "src/composables/notification"
export default {
	async preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		if (
			[
				"personal_producer_products_detail_show",
				"personal_producer_products_detail_create"
			].includes(previousRoute.name)
		)
			return

		Loading.show({
			spinnerColor: "primary",
		})

		const producer_store = useProducerStore(store)

		await producer_store.getProducerProductList(
			parseInt(currentRoute.params.producer_id)
		)
			.then(() =>
				Loading.hide()
			)
	},
	components: {
		ProducerProductList
	},
	setup() {
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const $router = useRouter()

		const { hasPermission } = useUserPermission()
		const { notifySuccess, notifyError } = useNotification()

		const tab = ref("common")

		const producer_product_setting_list = ref(null)

		const user_teams = computed(() => team_store.user_teams)

		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		return {
			team,
			is_able_to_manage_product,
			tab,
			producer_product_setting_list
		}
	}
}
</script>
