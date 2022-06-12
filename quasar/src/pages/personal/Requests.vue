<template>
	<ProducerJoinRequestList />
	<UserJoinProducerRequestList />
</template>

<script>
import ProducerJoinRequestList from "src/components/requests/ProducerJoinRequestList"
import UserJoinProducerRequestList from "src/components/requests/UserJoinProducerRequestList"
import { Loading } from "quasar"

export default {
	// preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
	// 	Loading.show({
	// 		spinnerColor: "primary",
	// 	})
	// 	return store.dispatch("user/getJoinRequestList").then(() => Loading.hide())
	// },
	components: { ProducerJoinRequestList, UserJoinProducerRequestList },
}
</script>
