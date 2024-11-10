<script setup>
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { computed, ref } from "vue"
import { today } from "@quasar/quasar-ui-qcalendar/src/index.js"
import { ORDER_BADGE_STATUS_TO_CLASS } from "src/const/orderStatuses"

const props = defineProps({
	isLoading: Boolean,
})

const emit = defineEmits([
	"change"
])

const producerOrdersStore = useProducerOrdersStore()

const calendar = ref(null)

const selectedDate = ref(today())

const orders = computed(() => producerOrdersStore.data)

const getOrders = (timestamp) => {
	return orders.value.filter((o) =>
		o[0].prepare_by === timestamp.date
	)
}

const newOrdersColumn = ref([
	{
		id: "new",
		label: "Новые"
	}
])

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
</script>

<template>
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
		<q-calendar-month
			ref="calendar"
			v-model="selectedDate"
			locale="ru-RU"
			date-type="round"
			no-active-date
			:weekday-class="getWeekdaysClass"
			:weekdays="[1,2,3,4,5,6,0]"
			:day-min-height="120"
			animated
			@change="$emit('change', $event)"
		>
			<template #day="{ scope: { timestamp } }">
				<template
					v-for="(orderList, index) in getOrders(timestamp)"
					:key="index"
				>
					<q-badge
						v-for="order in orderList"
						:key="order.prepare_by"
						class="q-pa-sm full-width justify-center"
						:color="ORDER_BADGE_STATUS_TO_CLASS[order.status].color"
						:text-color="ORDER_BADGE_STATUS_TO_CLASS[order.status].textColor"
						:label="order.order_count"
					/>
				</template>
			</template>
		</q-calendar-month>
	</div>
</template>
