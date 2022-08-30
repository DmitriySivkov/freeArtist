<template>
	<q-scroll-area style="height: 250px;">
		<ProducerProductList
			v-model="selected_product_id"
			:products="team.products"
		/>
	</q-scroll-area>
	<q-separator />
	<q-scroll-area style="height: 400px;">
		<ProducerProductSettingList
			:selected-product="selected_product"
		/>
	</q-scroll-area>
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
	components: { ProducerProductList, ProducerProductSettingList },
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.team_id))
		)
		const selected_product_id = ref(null)
		const selected_product = computed(
			() => selected_product_id.value ?
				team.value.products.find((p) => p.id === selected_product_id.value) :
				{}
		)

		return {
			team,
			selected_product_id,
			selected_product
		}
	}
}
</script>
