<template>
	<ProducerList />
	<router-view />
</template>

<script>
import { Loading } from "quasar"
import { defineAsyncComponent } from "vue"
export default {
	components: {
		ProducerList: defineAsyncComponent(() => import("src/components/producers/ProducerList")),
	},
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("producer/getList").then(() => Loading.hide())
	},
}
</script>
