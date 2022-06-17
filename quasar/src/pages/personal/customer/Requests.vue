<template>
	<UserCoworkingRequestList />
</template>

<script>
import UserCoworkingRequestList from "src/components/requests/UserCoworkingRequestList"
import { Loading } from "quasar"

export default {
	// preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
	// 	Loading.show({
	// 		spinnerColor: "primary",
	// 	})
	// 	return store.dispatch("user/getJoinRequestList").then(() => Loading.hide())
	// },
	components: { UserCoworkingRequestList },
}
</script>
