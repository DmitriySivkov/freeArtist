<template>
	<div class="q-pa-md row q-col-gutter-sm">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<OrderCalendar />
		</div>
		<div class="col-xs-12 col-md-6">
			<CustomerOrderCalendarList v-if="isCustomer"/>
			<ProducerOrderCalendarList v-if="isProducer"/>
		</div>
	</div>
</template>

<script>
import OrderCalendar from "src/components/orders/OrderCalendar"
import CustomerOrderCalendarList from "src/components/orders/Customer/CustomerOrderCalendarList"
import ProducerOrderCalendarList from "src/components/orders/Producer/ProducerOrderCalendarList"
import { useUserRole } from "src/composables/userRole"
import { date, Loading } from "quasar"
import { computed } from "vue"

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
		const { user, hasUserRole } = useUserRole()

		const isCustomer = computed(() => hasUserRole(user.value.roles.customer))
		const isProducer = computed(() => hasUserRole(user.value.roles.producer))

		return {
			isCustomer,
			isProducer
		}
	}
}
</script>
