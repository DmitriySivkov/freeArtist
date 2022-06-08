<template>
	<q-page class="column justify-center">
		<ProducerJoinRequestList />
		<UserJoinProducerRequestList />
	</q-page>
</template>

<script>
import ProducerJoinRequestList from "src/components/producers/ProducerJoinRequestList"
import UserJoinProducerRequestList from "src/components/users/UserJoinProducerRequestList"
import { Loading } from "quasar"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("producer/getJoinRequests").then(() => Loading.hide())
	},
	components: { ProducerJoinRequestList, UserJoinProducerRequestList },
}
</script>
