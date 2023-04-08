<template>
	<UserOrderList />
</template>

<script>
import UserOrderList from "src/components/orders/User/UserOrderList.vue"
import { date, Loading } from "quasar"
import { useOrderStore } from "src/stores/order"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		const order_store = useOrderStore(store)

		Loading.show({
			spinnerColor: "primary",
		})

		return order_store.getOrders({
			filter: { date: date.formatDate(Date.now(), "DD.MM.YYYY") }
		}).then(() =>
			Loading.hide()
		)
	},
	components: {
		UserOrderList,
	},
}
</script>
