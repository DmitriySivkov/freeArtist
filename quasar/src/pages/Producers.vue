<template>
	<ProducerList />
	<router-view />
</template>

<script>
import { Loading } from "quasar"
import ProducerList from "src/components/producers/ProducerList"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("producer/getList").then(() => Loading.hide())
	},
	components: { ProducerList },
}
</script>
