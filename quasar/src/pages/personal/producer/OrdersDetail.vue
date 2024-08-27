<template>
	<div class="absolute column fit no-wrap">
		<div class="col">
			<div class="row justify-center q-col-gutter-xs q-my-md">
				<div class="col-auto">
					<q-btn
						label="Сбросить"
						:loading="isLoading"
						:disable="isLoading"
						@click="onToday()"
					>
						<template v-slot:loading>
							<q-spinner-gears color="primary" />
						</template>
					</q-btn>
				</div>
				<div class="col-auto">
					<q-btn
						label="Назад"
						:loading="isLoading"
						:disable="isLoading"
						@click="onPrev()"
					>
						<template v-slot:loading>
							<q-spinner-gears color="primary" />
						</template>
					</q-btn>
				</div>
				<div class="col-auto">
					<q-btn
						label="Вперед"
						:loading="isLoading"
						:disable="isLoading"
						@click="onNext()"
					>
						<template v-slot:loading>
							<q-spinner-gears color="primary" />
						</template>
					</q-btn>
				</div>
			</div>

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
					@change="calendarChanged"
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
import {
	QCalendarAgenda,
	today
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
	return orders.value.filter((t) =>
		t.prepare_by ?
			t.prepare_by === timestamp.date :
			t.created_date === timestamp.date
	)
}

const onToday = () => {
	calendar.value.moveToToday()
}
const onPrev = () => {
	calendar.value.prev()
}
const onNext = () => {
	calendar.value.next()
}

const getWeekdaysClass = (data) => {
	return {
		"bg-primary text-white orders-current-day": data.scope.timestamp.current,
		"bg-grey-2 orders-not-current-day": !data.scope.timestamp.current,
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

/** is triggered on component mount **/
const calendarChanged = ({ start, end }) => {
	producerOrdersStore.emptyData()
	loadOrders({ start, end })
}

const loadOrders = (dateRange) => {
	isLoading.value = true

	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			dateRange,
		}
	})

	// todo - catch
	promise.then((response) => {
		producerOrdersStore.commitData(response.data)
	})

	promise.finally(() => {
		isLoading.value = false
	})
}

onBeforeUnmount(() => {
	producerOrdersStore.emptyData()
})
</script>

<style>
.orders-current-day {
	font-weight: normal;
}

.orders-current-day .q-calendar__button {
	color: #606c71 !important;
	background-color: white !important;
	border-color: #d74720 !important;
	font-weight: bold;
}

.orders-not-current-day {
	font-weight: normal;
}

.orders-not-current-day .q-calendar__button {
	color: #606c71 !important;
	border-color: #d74720 !important;
	font-weight: bold;
	background: none !important;
}
</style>
