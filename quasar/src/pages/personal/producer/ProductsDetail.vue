<template>
	<ProducerProductList
		:products="team.products"
		:loading-product="is_loading"
		:is-able-to-manage-product="is_able_to_manage_product"
		:is-product-changed="is_product_changed"
		@productSelected="productSelected"
		@deleteProduct="deleteProduct"
		@updateProduct="updateProduct"
		@createProduct="createProduct"
	/>
	<ProducerProductSettingList
		v-if="selected_product"
		:selected-product="selected_product"
		:is-able-to-manage-product="is_able_to_manage_product"
		@productChanged="is_product_changed = $event"
	/>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import ProducerProductSettingList from "src/components/producers/ProducerProductSettingList.vue"
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
		ProducerProductList,
		ProducerProductSettingList
	},
	setup() {
		const user_store = useUserStore()
		const team_store = useTeamStore()
		const producer_store = useProducerStore()
		const $router = useRouter()
		const { hasPermission } = useUserPermission()
		const { notifySuccess, notifyError } = useNotification()

		const user_teams = computed(() => team_store.user_teams)

		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const selected_product = ref(null)
		const is_loading = ref(null)
		const is_product_changed = ref(false)

		const createProduct = () => {
			is_loading.value = true

			is_loading.value = false
		}

		const updateProduct = () => {
			is_loading.value = true

			producer_store.updateProducerProduct({
				product: selected_product.value
			})

			is_loading.value = false
		}

		const productSelected = (product) => {
			if (!product) {
				selected_product.value = null
				return
			}

			selected_product.value = product
		}

		const deleteProduct = (product) => {
			is_loading.value = product.id

			selected_product.value = null

			const promise = producer_store.deleteProducerProduct({
				product_id: product.id
			})

			promise.then(() => {
				producer_store.commitRemoveProducerProduct({
					producer_id: team.value.detailed.id,
					product_id: product.id
				})

				notifySuccess("Продукт '" + product.title + "' успешно удалён")
			})

			promise.catch((error) => {
				is_loading.value = null
				notifyError(error.response.data)
			})
		}

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		return {
			team,
			selected_product,
			is_loading,
			is_able_to_manage_product,
			productSelected,
			deleteProduct,
			createProduct,
			updateProduct,
			is_product_changed
		}
	}
}
</script>
