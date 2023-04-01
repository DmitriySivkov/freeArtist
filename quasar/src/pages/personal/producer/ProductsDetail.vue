<template>
	<div :class="{'sticky__common_top': selected_product}">
		<ProducerProductList
			:model-value="selected_product"
			@update:model-value="selectProduct"
			:products="team.products"
			:loading-product="loading_product_id"
			:is-able-to-manage-product="is_able_to_manage_product"
			:is-product-changed="!!product_changes"
			@deleteProduct="deleteProduct"
			@updateProduct="updateProduct"
			@createProduct="createProduct"
		/>

		<q-tabs
			v-if="selected_product"
			:model-value="tab"
			@update:model-value="tab = $event"
			active-color="white"
			active-bg-color="secondary"
			indicator-color="transparent"
			align="justify"
			class="text-white bg-indigo-10"
		>
			<q-tab
				name="common"
				label="Общие"
				class="q-pa-md"
			/>
			<q-tab
				name="composition"
				label="Состав"
				class="q-pa-md"
			/>
			<q-tab
				name="images"
				label="Изображения"
				class="q-pa-md"
			/>
		</q-tabs>
	</div>

	<ProducerProductSettingList
		v-if="selected_product"
		:key="producer_product_setting_list_component_key"
		:tab="tab"
		:selected-product="selected_product"
		:is-able-to-manage-product="is_able_to_manage_product"
		@productChanged="productChanged"
		@commitProduct="commitProduct"
	/>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList.vue"
import ProducerProductSettingList from "src/components/producers/ProducerProductSettingList.vue"
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

		const tab = ref("common")

		const user_teams = computed(() => team_store.user_teams)

		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($router.currentRoute.value.params.producer_id))
		)

		const producer_product_setting_list_component_key = ref(Math.random()) // to update 'default' values after update

		const selected_product = ref(null)

		const loading_product_id = ref(null)

		const product_changes = ref(null)

		const createProduct = () => {
			loading_product_id.value = -1

			loading_product_id.value = null
		}

		const updateProduct = () => {
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

				// actually update current component product
				selected_product.value = team.value.products.find((p) => p.id === response.data.product.id)

				loading_product_id.value = null

				product_changes.value = null

				// for updating non-reactive 'default' values in settings component
				producer_product_setting_list_component_key.value = Math.random()

				notifySuccess("Успешно")
			})

			promise.catch((error) => {
				loading_product_id.value = null

				notifyError(error.response.data)
			})
		}

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
			createProduct,
			updateProduct,
			commitProduct,
			productChanged,
			producer_product_setting_list_component_key,
			tab,
			product_changes
		}
	}
}
</script>
