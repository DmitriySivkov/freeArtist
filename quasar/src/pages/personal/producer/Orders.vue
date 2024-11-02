<script setup>
import {
	today
} from "@quasar/quasar-ui-qcalendar/src/index.js"

import { ref, computed, onMounted, onBeforeUnmount } from "vue"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"
import { Dialog } from "quasar"
import { useScreen } from "src/composables/screen"

import ProducerOrderCard from "src/components/orders/Producer/ProducerOrderCard.vue"
import OrderCardDetailDialog from "components/dialogs/OrderCardDetailDialog.vue"
import { ORDER_CARD_STATUS_TO_CLASS } from "src/const/orderStatuses"
import { usePrivateChannels } from "src/composables/privateChannels"

import { useProducerOrdersStore } from "src/stores/producerOrders"
import { useUserStore } from "src/stores/user"

// todo - заказы можно распределять между участниками
const $router = useRouter()

const { isSmallScreen } = useScreen()

const userStore = useUserStore()
const producerOrdersStore = useProducerOrdersStore()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const calendar = ref(null)

const selectedDate = ref(today())
const startDate = ref(today())
const endDate = ref(today())

const orders = computed(() => producerOrdersStore.data)

const assignees = ref([])

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
			order,
			assignees: assignees.value
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

onMounted(() => {
	const promise = api.get(`personal/producers/${team.value.detailed_id}/users`)

	promise.then((response) => {
		assignees.value = response.data
	})
})

const {
	connectProducerOrders,
	disconnectProducerOrders
} = usePrivateChannels()

connectProducerOrders()

onBeforeUnmount(() => {
	producerOrdersStore.emptyData()

	disconnectProducerOrders()
})
</script>

<template>
	<q-page class="column no-wrap">
		<div class="col-auto">
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
								label="Сегодня"
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
		</div>
		<div class="col row justify-center">
			<q-calendar-agenda
				ref="calendar"
				v-model="selectedDate"
				:weekday-class="getWeekdaysClass"
				date-type="rounded"
				:max-days="isSmallScreen ? 3 : 7"
				locale="ru-RU"
				:weekdays="[1,2,3,4,5,6,0]"
				animated
				@change="calendarChanged"
			>
				<template #day="{ scope: { timestamp } }">
					<ProducerOrderCard
						v-for="order in getOrders(timestamp)"
						:key="order.producer_order_id"
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

.q-calendar-agenda__pane {
	height: 100%
}
</style>
