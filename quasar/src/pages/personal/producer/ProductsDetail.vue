<template>
	<q-list v-if="is_able_to_manage_product">
		<q-item class="justify-end">
			<q-item-section
				side
				class="q-pr-none"
			>
				<q-btn
					icon="add"
					color="secondary"
					@click="createProduct()"
				/>
			</q-item-section>
		</q-item>
	</q-list>
	<ProducerProductList :products="team.products" />
	<div v-if="selected_product_id">
		<q-card
			flat
			class="row"
		>
			<q-card-section class="col-xs-6 col-md-8">
				Выберите настройки
			</q-card-section>
			<q-card-section
				class="col-xs-6 col-md-4"
				style="margin-left:0"
			>
				<q-btn
					label="Выбрать другой продукт"
					color="primary"
					@click="unselectProduct"
					class="q-pt-md q-pb-md"
				/>
			</q-card-section>
		</q-card>
		<ProducerProductSettingList
			:selected-product="selected_product"
			:is-able-to-manage-product="is_able_to_manage_product"
			@product-created="setProductId"
			@product-deleted="unselectProduct"
		/>
	</div>
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
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})

		const producer_store = useProducerStore(store)

		return producer_store.getProducerProductList(parseInt(currentRoute.params.producer_id))
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
		const $route = useRoute()
		const { user_teams } = useUserTeam()
		const { hasPermission } = useUserPermission()
		const user = computed(() => user_store.$state)
		const team = computed(() =>
			user_teams.value.find((t) => t.detailed.id === parseInt($route.params.producer_id))
		)
		const selected_product_id = ref(null)

		const selected_product = computed(
			() => selected_product_id.value && selected_product_id.value > 0 ?
				team.value.products.find((p) => p.id === selected_product_id.value) :
				{}
		)

		const setProductId = (product_id) => selected_product_id.value = product_id

		const unselectProduct = () => selected_product_id.value = null

		const createProduct = () => selected_product_id.value = -1

		const is_able_to_manage_product = computed(() =>
			hasPermission(team.value.id,"producer_product") ||
			team.value.user_id === user.value.data.id
		)

		return {
			team,
			selected_product_id,
			selected_product,
			unselectProduct,
			createProduct,
			setProductId,
			is_able_to_manage_product
		}
	}
}
</script>
