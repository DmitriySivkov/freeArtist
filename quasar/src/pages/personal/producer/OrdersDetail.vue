<template>
	<div class="absolute column fit no-wrap">
		<div class="col">

			<!--					<ProducerOrderList />-->

			<!--					<navigation-bar-->
			<!--						@today="onToday"-->
			<!--						@prev="onPrev"-->
			<!--						@next="onNext"-->
			<!--					/>-->

			<div class="row justify-center">
				<q-calendar-agenda
					ref="calendar"
					v-model="selectedDate"
					:weekday-class="getWeekdaysClass"
					date-type="rounded"
					view="week"
					locale="ru-RU"
					:weekdays="[1,2,3,4,5,6,0]"
					:day-min-height="200"
					animated
				>
					<template #day="{ scope: { timestamp } }">
						<ProducerOrderCard
							v-for="order in getOrders(timestamp)"
							:key="order.id"
							:order="order"
							:card-class="ORDER_CARD_STATUS_TO_CLASS[order.status]"
							@show="showOrder"
						/>
					</template>
				</q-calendar-agenda>
			</div>

		</div>
	</div>
</template>

<script setup>
// import ProducerOrderList from "src/components/orders/Producer/ProducerOrderList.vue"
import {
	QCalendarAgenda,
	today,
	isBetweenDates,
	parsed,
	padNumber
} from "@quasar/quasar-ui-qcalendar/src/index.js"
import "@quasar/quasar-ui-qcalendar/src/QCalendarVariables.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTransitions.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTask.sass"

import { ref, computed, onMounted, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { Dialog } from "quasar"

import ProducerOrderCard from "src/components/orders/Producer/ProducerOrderCard.vue"
import OrderCardDetailDialog from "components/dialogs/OrderCardDetailDialog.vue"
import { ORDER_CARD_STATUS_TO_CLASS } from "src/const/orderStatuses"

import { useProducerOrdersStore } from "src/stores/producerOrders"

const $router = useRouter()

const producerOrdersStore = useProducerOrdersStore()

const calendar = ref(null)

const selectedDate = ref(today())
const startDate = ref(today())
const endDate = ref(today())

const orders = computed(() => producerOrdersStore.data)

const isLoading = ref(true)

const getOrders = (timestamp) => {
	return orders.value.filter((t) => t.created_date === timestamp.date)
}

const getWeekdaysClass = (data) => {
	return {
		"bg-primary text-white orders-active-weekday": data.scope.timestamp.currentWeekday,
		"bg-grey-2 orders-inactive-weekday": !data.scope.timestamp.currentWeekday,
	}
}

const showOrder = (order) => {
	Dialog.create({
		component: OrderCardDetailDialog,
		componentProps: {
			order
		}
	})
}

onMounted(() => {
	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			date: null,
			status: null
		}
	})

	// todo - catch
	promise.then((response) => {
		producerOrdersStore.commitData(response.data)
	})

	promise.finally(() => {
		isLoading.value = false
	})

})

onBeforeUnmount(() => {
	producerOrdersStore.emptyData()
})
</script>

<style>
.orders-active-weekday {
	font-weight: normal;
}

.orders-active-weekday .q-calendar__button {
	color: #606c71 !important;
	background-color: white !important;
	border-color: #d74720 !important;
	font-weight: bold;
}

.orders-inactive-weekday {
	font-weight: normal;
}

.orders-inactive-weekday .q-calendar__button {
	color: #606c71 !important;
	border-color: #d74720 !important;
	font-weight: bold;
}
</style>
