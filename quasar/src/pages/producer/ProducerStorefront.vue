<template>
	<ProducerStorefront/>
</template>

<script>
import ProducerStorefront from "src/components/producers/ProducerStorefront.vue"
import { Loading } from "quasar"
import { useProducerStore } from "src/stores/producer"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})

		const producer_store = useProducerStore(store)

		return producer_store.getProducer(parseInt(currentRoute.params.producer_id))
			.then(() =>
				Loading.hide()
			)
	},
	components: { ProducerStorefront }
}
</script>
