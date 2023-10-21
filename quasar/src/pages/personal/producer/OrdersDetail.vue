<template>
	<div class="row justify-center">
		<div class="col-xs-12 col-md-8 col-lg-7 col-xl-6">
			<OrderCalendar
				v-model="qdate"
				class="q-mb-sm"
				:isLoading="isLoading"
			/>
			<ProducerOrderList
				:date="qdate"
				@load="isLoading = $event"
			/>
		</div>
	</div>
</template>

<script setup>
import OrderCalendar from "src/components/orders/OrderCalendar.vue"
import ProducerOrderList from "src/components/orders/Producer/ProducerOrderList.vue"
import { ref } from "vue"
import { date } from "quasar"

const isLoading = ref(true)
const qdate = ref(date.formatDate(Date.now(), "DD.MM.YYYY"))
</script>
