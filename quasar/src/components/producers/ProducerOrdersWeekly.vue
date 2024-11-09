<script setup>
import { useScreen } from "src/composables/screen"
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { computed, ref } from "vue"
import { today } from "@quasar/quasar-ui-qcalendar/src/index.js"
import { Dialog } from "quasar"
import ProducerOrderCard from "src/components/orders/Producer/ProducerOrderCard.vue"
import ProducerNewOrderCard from "src/components/orders/Producer/ProducerNewOrderCard.vue"
import OrderCardDetailDialog from "src/components/dialogs/OrderCardDetailDialog.vue"
import NewOrderCardDetailDialog from "src/components/dialogs/NewOrderCardDetailDialog.vue"
import { ORDER_CARD_STATUS_TO_CLASS, ORDER_STATUSES } from "src/const/orderStatuses"

const props = defineProps({
	isLoading: Boolean,
	isInitializing: Boolean,
	assignees: {
		type: Array,
		default: () => []
	},
	newOrders: {
		type: Array,
		default: () => []
	}
})

const emit = defineEmits([
	"initialized",
	"change",
	"acceptOrder"
])

const { isSmallScreen } = useScreen()

const producerOrdersStore = useProducerOrdersStore()

const calendar = ref(null)

const selectedDate = ref(today())

const orders = computed(() => producerOrdersStore.data)

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

const showOrder = (order) => {
	Dialog.create({
		component: OrderCardDetailDialog,
		componentProps: {
			order,
			assignees: props.assignees,
		}
	})
}

const showNewOrder = (order) => {
	Dialog.create({
		component: NewOrderCardDetailDialog,
		componentProps: {
			order,
		}
	}).onOk(() => {
		emit("acceptOrder", order.id)
	})
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
		<q-calendar-agenda
			ref="calendar"
			v-model="selectedDate"
			:weekday-class="getWeekdaysClass"
			date-type="round"
			:max-days="isSmallScreen ? 3 : 7"
			locale="ru-RU"
			:weekdays="[1,2,3,4,5,6,0]"
			:left-column-options="newOrdersColumn"
			animated
			@change="$emit('change', $event)"
		>
			<template #head-column-label="{ scope: { column: { id, label } } }">
				<template v-if="id === 'new'">
					<span class="ellipsis text-weight-regular">{{ label }}</span>
				</template>
			</template>

			<template #column="{ scope: { column } }">
				<template v-if="column.id === 'new'">
					<ProducerNewOrderCard
						v-for="order in newOrders"
						:key="order.producer_order_id"
						:order="order"
						:card-class="ORDER_CARD_STATUS_TO_CLASS[ORDER_STATUSES.NEW]"
						@show="showNewOrder"
					/>
				</template>
			</template>

			<template #day="{ scope: { timestamp } }">
				<ProducerOrderCard
					v-for="order in getOrders(timestamp)"
					:key="order.producer_order_id"
					:order="order"
					:hasMultipleAssignees="assignees.length > 1"
					:card-class="ORDER_CARD_STATUS_TO_CLASS[order.status]"
					@show="showOrder"
				/>
			</template>
		</q-calendar-agenda>
	</div>
</template>
