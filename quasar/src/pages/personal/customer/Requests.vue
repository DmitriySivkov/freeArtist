<template>
	<UserJoinProducerRequestList />
</template>

<script>
import UserJoinProducerRequestList from "src/components/requests/UserJoinProducerRequestList"
import { Loading } from "quasar"

export default {
	// preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
	// 	Loading.show({
	// 		spinnerColor: "primary",
	// 	})
	// 	return store.dispatch("user/getJoinRequestList").then(() => Loading.hide())
	// },
	components: { UserJoinProducerRequestList },
}
</script>
