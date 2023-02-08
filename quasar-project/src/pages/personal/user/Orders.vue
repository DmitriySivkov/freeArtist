<template>
	<div class="q-pa-md row q-col-gutter-sm">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<OrderCalendar />
		</div>
		<div class="col-xs-12 col-md-6">
			<CustomerOrderCalendarList />
		</div>
	</div>
</template>

<script>
import OrderCalendar from "src/components/orders/OrderCalendar"
import CustomerOrderCalendarList from "src/components/orders/Customer/CustomerOrderCalendarList"
import { date, Loading } from "quasar"
import { useOrderStore } from "src/stores/order"
export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
    const order_store = useOrderStore(store)

		Loading.show({
			spinnerColor: "primary",
		})

		return order_store.getList({
			filter: { date: date.formatDate(Date.now(), "DD.MM.YYYY") }
		}).then(() => Loading.hide())
	},
	components: {
		OrderCalendar,
		CustomerOrderCalendarList,
	},
}
</script>
