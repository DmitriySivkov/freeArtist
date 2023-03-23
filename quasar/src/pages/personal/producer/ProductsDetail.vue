<template>
	<ProducerProductList
		:products="team.products"
		:loading-product="loading_product_id"
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
		@productChanged="productChanged"
		@selectProduct="selectProduct"
		@commitProduct="commitProduct"
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

		const loading_product_id = ref(null)
		const is_product_changed = ref(false)
		const product_changes = ref(null)

		const createProduct = () => {
			loading_product_id.value = -1

			loading_product_id.value = null
		}

		const updateProduct = async() => {
			if (!product_changes.value)
				return

			loading_product_id.value = selected_product.value.id

			const promise = producer_store.updateProducerProduct({
				product: selected_product.value,
				changes: product_changes.value
			})

			promise.then((response) => {
				let fields = {}

				if (!!response.data.changes.common) {
					fields = {
						...fields,
						title: response.data.product.title,
						price: response.data.product.price,
						amount: response.data.product.amount
					}
				}

				if (!!response.data.changes.composition) {
					fields = {
						...fields,
						composition: response.data.product.composition,
					}
				}

				if (!!response.data.changes.images) {
					fields = {
						...fields,
						images: response.data.product.images,
					}
				}

				producer_store.commitProducerProductFields({
					producer_id: response.data.product.producer_id,
					product_id: response.data.product.id,
					fields
				})

				loading_product_id.value = null

				notifySuccess("Успешно")
			})

			promise.catch(() => {
				loading_product_id.value = null

				notifyError("Не удалось")
			})
		}

		const productSelected = (product) => {
			if (product === null) {
				selected_product.value = null
				is_product_changed.value = false
				return
			}

			selected_product.value = product
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

				notifySuccess("Продукт '" + product.title + "' успешно удалён")
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

		const selectProduct = (new_product_value) => {
			selected_product.value = new_product_value
		}

		const commitProduct = (new_product_value) => {
			selected_product.value = new_product_value
		}

		const productChanged = ({ is_changed, changes }) => {
			is_product_changed.value = is_changed

			product_changes.value = changes
		}

		return {
			team,
			selected_product,
			loading_product_id,
			is_able_to_manage_product,
			productSelected,
			deleteProduct,
			createProduct,
			updateProduct,
			selectProduct,
			commitProduct,
			productChanged,
			is_product_changed
		}
	}
}
</script>
