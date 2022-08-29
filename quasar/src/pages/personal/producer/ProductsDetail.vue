<template>
	<ProducerProductList
		:products="team.products"
	/>
</template>

<script>
import ProducerProductList from "src/components/producers/ProducerProductList"
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed } from "vue"
import { Loading } from "quasar"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("userProducer/getProducerProductList", parseInt(currentRoute.params.id))
			.then(() => Loading.hide())
	},
	components: { ProducerProductList },
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.id))
		)

		return {
			team
		}
	}
}
</script>
