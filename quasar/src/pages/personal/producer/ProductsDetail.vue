<template>
	<q-list
		v-if="is_able_to_manage_product && !selected_product && !is_creating_product"
	>
		<q-item class="justify-end">
			<q-item-section class="q-pr-none col-xs-3 col-md-2 col-lg-1">
				<q-btn
					icon="add"
					color="secondary"
					@click="createProduct"
				/>
			</q-item-section>
		</q-item>
	</q-list>
	<ProducerProductList
		:products="team.products"
		:loading-product="loading_product"
		:is-creating-product="is_creating_product"
		:is-able-to-manage-product="is_able_to_manage_product"
		:is-product-changed="is_product_changed"
		@productSelected="productSelected"
		@productDeleted="productDeleted"
	/>
	<ProducerProductSettingList
		v-if="selected_product || is_creating_product"
		:is-creating-product="is_creating_product"
		:selected-product="selected_product"
		:is-able-to-manage-product="is_able_to_manage_product"
		@productChanged="productChanged"
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

		await producer_store.getProducerProductList(parseInt(currentRoute.params.producer_id))
			.then(() => Loading.hide())
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

		const is_creating_product = ref(false)
		const selected_product = ref(null)
		const loading_product = ref(null)

		const createProduct = () => {
			is_creating_product.value = true
		}

		const productSelected = (product_id) => {
			if (!product_id) {
				selected_product.value = null
				return
			}

			selected_product.value = team.value.products.find((p) => p.id === product_id)
		}

		const productDeleted = (product) => {
			loading_product.value = product.id

			selected_product.value = null

			const promise = producer_store.deleteProducerProduct({
				producer_id: parseInt($router.currentRoute.value.params.producer_id),
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
				loading_product.value = null
				notifyError(error.response.data)
			})
		}

		const is_product_changed = ref(false)

		const productChanged = (is_changed) => {
			is_product_changed.value = is_changed
		}

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user_store.data.id
		)

		return {
			team,
			selected_product,
			loading_product,
			is_able_to_manage_product,
			productSelected,
			productDeleted,
			createProduct,
			productChanged,
			is_creating_product,
			is_product_changed
		}
	}
}
</script>
