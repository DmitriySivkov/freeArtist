<template>
	<q-list
		v-if="is_able_to_manage_product && !selected_product"
	>
		<q-item class="justify-end">
			<q-item-section class="q-pr-none col-xs-3 col-md-2 col-lg-1">
				<q-btn
					icon="add"
					color="secondary"
				/>
			</q-item-section>
		</q-item>
	</q-list>
	<ProducerProductList
		:products="team.products"
		:is-able-to-manage-product="is_able_to_manage_product"
		@productSelected="productSelected"
	/>
	<ProducerProductSettingList
		v-if="selected_product"
		:selected-product="selected_product"
		:is-able-to-manage-product="is_able_to_manage_product"
	/>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import ProducerProductSettingList from "src/components/producers/ProducerProductSettingList.vue"
import { useRoute } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed, ref } from "vue"
import { Loading } from "quasar"
import { useUserPermission } from "src/composables/userPermission"
import { useUserStore } from "src/stores/user"
import { useProducerStore } from "src/stores/producer"
export default {
	async preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})

		const producer_store = useProducerStore(store)

		await producer_store.getProducerProductList(parseInt(currentRoute.params.producer_id))
			.then(() => Loading.hide())
	},
	components: {
		ProducerProductList,
		ProducerProductSettingList
	},
	setup() {
		const user_store = useUserStore()
		const $route = useRoute()
		const { user_teams } = useUserTeam()
		const { hasPermission } = useUserPermission()

		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($route.params.producer_id))
		)

		const selected_product = ref(null)

		const productSelected = (product_id) => {
			if (!product_id) {
				selected_product.value = null
				return
			}

			selected_product.value = team.value.products.find((p) => p.id === product_id)
		}

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		return {
			team,
			selected_product,
			is_able_to_manage_product,
			productSelected
		}
	}
}
</script>
