<template>
	<UserRequestList />
</template>

<script>
import UserRequestList from "components/requests/UserRequestList"
import { Loading } from "quasar"

export default {
	// preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
	// 	Loading.show({
	// 		spinnerColor: "primary",
	// 	})
	// 	return store.dispatch("user/getJoinRequestList").then(() => Loading.hide())
	// },
	components: { UserRequestList },
}
</script>
