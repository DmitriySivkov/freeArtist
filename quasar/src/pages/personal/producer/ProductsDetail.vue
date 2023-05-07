<template>
	<div class="absolute full-width full-height sticky__common_top">
		<ProducerProductList
			:products="team.products"
			:loading-product="loading_product_id"
			:is-able-to-manage-product="is_able_to_manage_product"
			@deleteProduct="deleteProduct"
		/>
	</div>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import { useRouter } from "vue-router"
import { computed, ref } from "vue"
import _ from "lodash"
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

		const selected_product = ref(null)

		const loading_product_id = ref(null)

		const product_changes = ref(null)

		const selectProduct = (product) => {
			product_changes.value = null

			selected_product.value = product ? _.cloneDeep(product) : null
		}

		const deleteProduct = (product) => {
			loading_product_id.value = product.id

			selected_product.value = null

			const promise = producer_store.deleteProducerProduct({
				product_id: product.id
			})

			promise.then(() => {
				producer_store.commitRemoveProducerProduct({
					producer_id: team.value.detailed.id,
					product_id: product.id
				})

				notifySuccess("Продукт «" + product.title + "» успешно удалён")
			})

			promise.catch((error) => {
				loading_product_id.value = null
				notifyError(error.response.data)
			})
		}

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		const commitProduct = (new_product_value) => {
			selected_product.value = new_product_value
		}

		const productChanged = (changes) => {
			// if product value is default
			if (Object.values(changes).filter((c) => !!c).length === 0) {
				product_changes.value = null
				return
			}

			product_changes.value = changes
		}

		return {
			team,
			selected_product,
			loading_product_id,
			is_able_to_manage_product,
			selectProduct,
			deleteProduct,
			commitProduct,
			productChanged,
			tab,
			product_changes,
			producer_product_setting_list
		}
	}
}
</script>
