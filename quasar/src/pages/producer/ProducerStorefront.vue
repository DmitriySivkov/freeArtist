<template>
	<ProducerStorefront/>
</template>

<script>
import ProducerStorefront from "src/components/producers/ProducerStorefront"
import { Loading } from "quasar"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("producer/getProducer", parseInt(currentRoute.params.producer_id)).then(() =>
			Loading.hide()
		)
	},
	components: { ProducerStorefront }
}
</script>
