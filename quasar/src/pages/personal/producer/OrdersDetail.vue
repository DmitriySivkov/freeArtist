<template>
	<div class="q-pa-sm row q-col-gutter-sm">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<OrderCalendar
				v-model="qdate"
			/>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-9">
			<ProducerOrderList
				:date="qdate"
			/>
		</div>
	</div>
</template>

<script setup>
import OrderCalendar from "src/components/orders/OrderCalendar.vue"
import ProducerOrderList from "src/components/orders/Producer/ProducerOrderList.vue"
import { ref } from "vue"
import { date } from "quasar"

const qdate = ref(date.formatDate(Date.now(), "DD.MM.YYYY"))
</script>
