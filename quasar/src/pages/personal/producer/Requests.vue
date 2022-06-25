<template>
	<ProducerRequestList />
</template>

<script>
import ProducerRequestList from "components/requests/ProducerRequestList"
import { Loading } from "quasar"

export default {
	// preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
	// 	Loading.show({
	// 		spinnerColor: "primary",
	// 	})
	// 	return store.dispatch("user/getJoinRequestList").then(() => Loading.hide())
	// },
	components: { ProducerRequestList },
}
</script>
