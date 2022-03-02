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

export default {
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
