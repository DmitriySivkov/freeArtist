<template>
	<div class="q-pa-md row q-col-gutter-sm">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<OrderCalendar />
		</div>
		<div class="col-xs-12 col-md-6">
			<CustomerOrderCalendarList v-if="user.data.role_id === user.roles.customer"/>
			<ProducerOrderCalendarList v-if="user.data.role_id === user.roles.producer"/>
		</div>
	</div>
</template>

<script>
import OrderCalendar from "src/components/orders/OrderCalendar"
import CustomerOrderCalendarList from "src/components/orders/Customer/CustomerOrderCalendarList"
import ProducerOrderCalendarList from "src/components/orders/Producer/ProducerOrderCalendarList"
import { computed } from "vue"
import { useStore } from "vuex"
import { date, Loading } from "quasar"

export default {
	preFetch ({ store, currentRoute, previousRoute, redirect, ssrContext, urlPath, publicPath }) {
		Loading.show({
			spinnerColor: "primary",
		})
		return store.dispatch("order/getList", {
			filter: { date: date.formatDate(Date.now(), "DD.MM.YYYY") }
		}).then(() => Loading.hide())
	},
	components: {
		OrderCalendar,
		CustomerOrderCalendarList,
		ProducerOrderCalendarList
	},
	setup() {
		const $store = useStore()
		const user = computed(() => $store.state.user)

		return {
			user
		}
	}
}
</script>
