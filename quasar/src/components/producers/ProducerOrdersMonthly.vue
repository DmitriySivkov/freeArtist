<script setup>
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { computed, ref } from "vue"
import { today } from "@quasar/quasar-ui-qcalendar/src/index.js"
import { api } from "src/boot/axios"

const props = defineProps({
})

const emit = defineEmits([
	"change"
])

const producerOrdersStore = useProducerOrdersStore()

const calendar = ref(null)

const selectedDate = ref(today())

const newOrders = ref([])
const orders = computed(() => producerOrdersStore.data)

const isLoading = ref(false)

const getOrders = (timestamp) => {
	return orders.value.filter((o) =>
		o.prepare_by === timestamp.date
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

/** is triggered on component mount **/
const calendarChanged = ({ start, end }) => {
	producerOrdersStore.emptyData()
	loadOrders({ start, end })
}

const loadOrders = (dateRange) => {
	isLoading.value = true

	const promise = api.get(`personal/producers/${props.producerId}/orders`,{
		params: {
			dateRange,
		}
	})

	// todo - catch
	promise.then((response) => {
		producerOrdersStore.commitData(response.data)
	})

	promise.finally(() => isLoading.value = false)
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
			:weekday-class="getWeekdaysClass"
			:weekdays="[1,2,3,4,5,6,0]"
			:day-min-height="80"
			animated
			@change="$emit('change', $event)"
		/>
	</div>
</template>
