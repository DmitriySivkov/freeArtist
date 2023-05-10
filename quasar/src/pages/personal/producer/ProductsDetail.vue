<template>
	<ProducerProductList
		:products="team.products"
		:is-able-to-manage-product="is_able_to_manage_product"
	/>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import { useRouter } from "vue-router"
import { computed } from "vue"
import { Loading } from "quasar"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
import { useTeamStore } from "src/stores/team"
export default {
	async preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		if (
			[
				"personal_producer_products_detail_show",
				"personal_producer_products_detail_create"
			].includes(previousRoute.name) && !currentRoute.query.force_load
		)
			return

		Loading.show({
			spinnerColor: "primary",
		})
		// todo - try to fetch products locally - not bounding to pinia
		const producer_store = useProducerStore(store)

		await producer_store.getProducerProductList(
			parseInt(currentRoute.params.producer_id)
		).then(() => Loading.hide())
	},
	components: {
		ProducerProductList
	},
	setup() {
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const $router = useRouter()

		const { hasPermission } = useUserPermission()

		const team = computed(() =>
			team_store.user_teams.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		return {
			team,
			is_able_to_manage_product
		}
	}
}
</script>
