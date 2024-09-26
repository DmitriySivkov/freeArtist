<script setup>
import {
	today
} from "@quasar/quasar-ui-qcalendar/src/index.js"

import { ref, computed, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { Dialog } from "quasar"

import ProducerOrderCard from "src/components/orders/Producer/ProducerOrderCard.vue"
import OrderCardDetailDialog from "components/dialogs/OrderCardDetailDialog.vue"
import { ORDER_CARD_STATUS_TO_CLASS } from "src/const/orderStatuses"

import { useProducerOrdersStore } from "src/stores/producerOrders"
import { useTeamStore } from "src/stores/team"

// todo - заказы можно распределять между участниками
const $router = useRouter()

const teamStore = useTeamStore()
const producerOrdersStore = useProducerOrdersStore()

const team = computed(() =>
	teamStore.user_teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

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

	const promise = api.get(`personal/producers/${team.value.detailed_id}/orders`,{
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

<template>
	<q-page>
		<div class="row justify-center">
			<div class="col-xs-12 col-sm-8 col-md-5 col-lg-4 q-pa-sm">
				<div class="row justify-between">
					<div class="col-auto">
						<q-btn
							label="Назад"
							color="secondary"
							:loading="isLoading"
							:disable="isLoading"
							@click="onPrev()"
						>
							<template v-slot:loading>
								<q-spinner-gears color="white" />
							</template>
						</q-btn>
					</div>
					<div class="col-auto">
						<q-btn
							label="Сбросить"
							color="secondary"
							:loading="isLoading"
							:disable="isLoading"
							@click="onToday()"
						>
							<template v-slot:loading>
								<q-spinner-gears color="white" />
							</template>
						</q-btn>
					</div>
					<div class="col-auto">
						<q-btn
							label="Вперед"
							color="secondary"
							:loading="isLoading"
							:disable="isLoading"
							@click="onNext()"
						>
							<template v-slot:loading>
								<q-spinner-gears color="white" />
							</template>
						</q-btn>
					</div>
				</div>
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
	</q-page>
</template>

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
