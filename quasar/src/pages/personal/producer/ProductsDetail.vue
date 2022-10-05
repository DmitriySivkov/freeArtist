<template>
	<div>
		<div
			class="row q-col-gutter-xs q-ma-lg items-end"
			v-if="!selected_product_id"
		>
			<div class="col-xs-6">
				Выберите продукт
			</div>
			<div class="col-xs-6 offset-md-4 col-md-2">
				<q-btn
					size="lg"
					icon="add"
					color="primary"
					class="full-width"
					@click="createProduct()"
				/>
			</div>
		</div>
		<ProducerProductList
			v-model="selected_product_id"
			:products="team.products"
		/>
	</div>
	<div v-if="selected_product_id">
		<q-item>
			<q-item-section class="col-xs-6 col-md-8">
				Выберите настройки
			</q-item-section>
			<q-item-section
				class="col-xs-6 col-md-4"
				style="margin-left:0"
			>
				<q-btn
					v-if="selected_product_id"
					label="Выбрать другой продукт"
					color="primary"
					@click="unselectProduct"
					class="q-pt-md q-pb-md"
				/>
			</q-item-section>
		</q-item>
		<ProducerProductSettingList
			:selected-product="selected_product"
			@product-created="setProductId"
		/>
	</div>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList"
import ProducerProductSettingList from "src/components/producers/ProducerProductSettingList"
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed, ref } from "vue"
import { Loading } from "quasar"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("userProducer/getProducerProductList", parseInt(currentRoute.params.team_id))
			.then(() => Loading.hide())
	},
	components: {
		ProducerProductList,
		ProducerProductSettingList
	},
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.team_id))
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

		return {
			team,
			selected_product_id,
			selected_product,
			unselectProduct,
			createProduct,
			setProductId
		}
	}
}
</script>
