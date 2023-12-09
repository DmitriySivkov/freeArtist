<template>
	<div class="column q-gutter-xs full-height">
		<ProducerOrderListBoard
			:board-id="ORDER_STATUSES.NEW"
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.NEW]"
			:orders="orderList[ORDER_STATUSES.NEW]"
			:board-class="'bg-blue-1'"
			:card-class="'text-white bg-blue-9'"
			:chip-color="'blue-9'"
			:ghost-class="$style.ghost__new"
			:draggable-container-class="$style.board__scrollable"
			:is-mounting="isMounting"
			is-sortable
			@show="showOrderDetails({
				order: $event,
				popupClass: 'text-white bg-blue-9'
			})"
			@change="moveOrder"
		/>

		<ProducerOrderListBoard
			:board-id="ORDER_STATUSES.PROCESS"
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.PROCESS]"
			:orders="orderList[ORDER_STATUSES.PROCESS]"
			:board-class="'bg-green-1'"
			:card-class="'text-white bg-green-9'"
			:chip-color="'green-9'"
			:ghost-class="$style.ghost__process"
			:draggable-container-class="$style.board__scrollable"
			:is-mounting="isMounting"
			is-sortable
			@show="showOrderDetails({
				order: $event,
				popupClass: 'text-white bg-green-9'
			})"
			@change="moveOrder"
		/>

		<ProducerOrderListBoard
			:board-id="ORDER_STATUSES.CANCEL"
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.CANCEL]"
			:orders="orderList[ORDER_STATUSES.CANCEL]"
			:board-class="'bg-red-1'"
			:card-class="'text-white bg-red-9'"
			:chip-color="'red-9'"
			:ghost-class="$style.ghost__cancel"
			:draggable-container-class="$style.board__scrollable"
			:is-mounting="isMounting"
			with-calendar
			:loading-board="loadingBoard"
			@show="showOrderDetails({
				order: $event,
				popupClass: 'text-white bg-red-9'
			})"
			@change="moveOrder"
			@calendar="loadOrders({
				date:$event,
				status:ORDER_STATUSES.CANCEL,
				isMount:false
			})"
		/>

		<ProducerOrderListBoard
			:board-id="ORDER_STATUSES.DONE"
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.DONE]"
			:orders="orderList[ORDER_STATUSES.DONE]"
			:board-class="'bg-grey-1'"
			:card-class="'text-white bg-grey-8'"
			:chip-color="'grey-8'"
			:ghost-class="$style.ghost__done"
			:draggable-container-class="$style.board__scrollable"
			:is-mounting="isMounting"
			with-calendar
			:loading-board="loadingBoard"
			@show="showOrderDetails({
				order: $event,
				popupClass: 'text-white bg-grey-8'
			})"
			@change="moveOrder"
			@calendar="loadOrders({
				date:$event,
				status:ORDER_STATUSES.DONE,
				isMount:false
			})"
		/>

	</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { Dialog } from "quasar"
import { debounce } from "lodash"
import { ORDER_STATUSES, ORDER_STATUS_NAMES } from "src/const/orderStatuses"
import OrderCardDetailDialog from "src/components/dialogs/OrderCardDetailDialog.vue"
import ProducerOrderListBoard from "src/components/orders/Producer/ProducerOrderListBoard.vue"
import { useNotification } from "src/composables/notification"
import { map, mapValues } from "lodash"

const $router = useRouter()
// todo - calendar + init load + backend controllers
const { notifyError } = useNotification()

const orderList = ref({
	[ORDER_STATUSES.NEW]: [],
	[ORDER_STATUSES.PROCESS]: [],
	[ORDER_STATUSES.CANCEL]: [],
	[ORDER_STATUSES.DONE]: []
})

const isMounting = ref(true)

onMounted(() => {
	loadOrders({
		date: null,
		status: null,
		isMount: true
	})
})

const loadingBoard = ref(null)

const loadOrders = async({ date, status, isMount }) => {
	if (status) {
		loadingBoard.value = status
	}

	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			date,
			status
		}
	})

	// todo - catch
	promise.then((response) => {
		if (!status) {
			orderList.value = response.data
		} else {
			orderList.value[status] = response.data
		}
	})

	promise.finally(() => {
		if (isMount) {
			isMounting.value = false
		}
		if (loadingBoard.value) {
			loadingBoard.value = null
		}
	})
}

const showOrderDetails = ({order, popupClass}) => {
	Dialog.create({
		component: OrderCardDetailDialog,
		componentProps: {
			producerId: $router.currentRoute.value.params.producer_id,
			order,
			popupClass
		}
	})
}

const movableOrders = ref({})

const moveOrder = debounce(() => {
	const promise = api.post(
		`personal/producers/${$router.currentRoute.value.params.producer_id}/orders/move`,
		mapValues(orderList.value, (orderStatusList) => map(orderStatusList, "id"))
	)

	promise.catch(() => {
		notifyError("Что-то пошло не так") // todo - return back failed orders
	})

	promise.finally(() => movableOrders.value = {})
	// //todo - clear movableOrders on success
}, 3000)
</script>

<style lang="scss" module>
.ghost {
	&__new {
		opacity: 0.5;
		background: #1565c0;
	}
	&__process {
		opacity: 0.5;
		background: #2e7d32;
	}
	&__cancel {
		opacity: 0.5;
		background: #c62828;
	}
	&__done {
		opacity: 0.5;
		background: #616161;
	}
}
.board {
	&__scrollable {
		overflow-y: scroll;
		&::-webkit-scrollbar {
			display: none;
		}
	}
}
</style>
