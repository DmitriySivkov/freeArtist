<template>
	<div class="column q-gutter-xs full-height">
		<ProducerOrderListBoard
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.NEW]"
			:orders="orderList[ORDER_STATUSES.NEW]"
			:board-class="'bg-blue-1'"
			:card-class="'text-white bg-blue-9'"
			:chip-color="'blue-9'"
			:ghost-class="$style.ghost__new"
			@show="showOrderDetails"
		/>

		<ProducerOrderListBoard
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.PROCESS]"
			:orders="orderList[ORDER_STATUSES.PROCESS]"
			:board-class="'bg-green-1'"
			:card-class="'text-white bg-green-9'"
			:chip-color="'green-9'"
			:ghost-class="$style.ghost__process"
			@show="showOrderDetails"
		/>

		<ProducerOrderListBoard
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.CANCEL]"
			:orders="orderList[ORDER_STATUSES.CANCEL]"
			:board-class="'bg-red-1'"
			:card-class="'text-white bg-red-9'"
			:chip-color="'red-9'"
			:ghost-class="$style.ghost__cancel"
			@show="showOrderDetails"
		/>

		<ProducerOrderListBoard
			:board-name="ORDER_STATUS_NAMES[ORDER_STATUSES.DONE]"
			:orders="orderList[ORDER_STATUSES.DONE]"
			:board-class="'bg-grey-1'"
			:card-class="'text-white bg-grey-8'"
			:chip-color="'grey-8'"
			:ghost-class="$style.ghost__done"
			@show="showOrderDetails"
		/>

	</div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { Dialog } from "quasar"
import { ORDER_STATUSES, ORDER_STATUS_NAMES } from "src/const/orderStatuses"
import OrderCardDetailDialog from "src/components/dialogs/OrderCardDetailDialog.vue"
import ProducerOrderListBoard from "src/components/orders/Producer/ProducerOrderListBoard.vue"

const props = defineProps({
	date: [String, Object]
})

const emit = defineEmits([
	"load"
])

const $router = useRouter()

const orderList = ref({
	[ORDER_STATUSES.NEW]: [],
	[ORDER_STATUSES.PROCESS]: [],
	[ORDER_STATUSES.CANCEL]: [],
	[ORDER_STATUSES.DONE]: []
})

onMounted(() => {
	loadOrders(props.date)
})

const loadOrders = (date) => {
	emit("load", true)

	const promise = api.get(`personal/producers/${$router.currentRoute.value.params.producer_id}/orders`,{
		params: {
			date
		}
	})

	// todo - catch
	promise.then((response) => {
		orderList.value = response.data
	})

	promise.finally(() => emit("load", false))
}

const showOrderDetails = (order) => {
	Dialog.create({
		component: OrderCardDetailDialog,
		componentProps: { order }
	})
}

watch(
	() => props.date,
	(date) => loadOrders(date)
)
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
</style>
