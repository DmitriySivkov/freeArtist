<template>
	<div class="column q-gutter-xs full-height">
		<q-card
			class="col bg-blue-1"
		>
			<div class="column full-height q-pb-xs">
				<div class="col-shrink">
					<div class="row q-py-sm">
						<q-chip
							square
							color="blue-9"
							text-color="white"
						>
							{{ ORDER_STATUS_NAMES[ORDER_STATUSES.NEW] }}
						</q-chip>
					</div>
				</div>

				<!-- todo - visible scrollbar -->
				<q-scroll-area
					class="col full-height"
					visible
					:vertical-thumb-style="{
						right: '4px',
						borderRadius: '7px',
						backgroundColor: '#027be3',
						width: '4px',
						opacity: 1
					}"
					:vertical-bar-style="{
						right: '2px',
						borderRadius: '9px',
						backgroundColor: 'primary',
						width: '8px',
						opacity: 0.2
					}"
				>
					<div class="absolute fit column no-wrap q-ml-xs">
						<div
							v-for="n in orderListStatusNewColumnCount"
							:key="n"
							class="col-6"
							:class="{'q-mb-xs': n !== orderListStatusNewColumnCount}"
						>
							<!-- todo - stretch and make gutter -->
							<div class="row full-height">
								<q-card
									v-for="(order, index) in orderList[ORDER_STATUSES.NEW].slice(cardsInARow*n - cardsInARow, cardsInARow*n)"
									:key="index"
									class="col-2 full-height q-mr-xs flex flex-center text-white bg-blue-9 cursor-pointer q-hoverable"
									@click="showOrderDetails(order)"
								>
									<span class="q-focus-helper"></span>
									<div class="text-center">
										#{{ order.id }}
									</div>
								</q-card>
							</div>
						</div>
					</div>
				</q-scroll-area>

			</div>
		</q-card>
		<q-card
			class="col bg-green-1"
		>
			<div class="column full-height">
				<div class="col-shrink">
					<div class="row q-py-sm">
						<q-chip
							square
							color="green-9"
							text-color="white"
						>
							{{ ORDER_STATUS_NAMES[ORDER_STATUSES.PROCESS] }}
						</q-chip>
					</div>
				</div>

				<!-- todo - visible scrollbar -->
				<q-scroll-area
					class="col full-height"
					visible
					:vertical-thumb-style="{
						right: '4px',
						borderRadius: '7px',
						backgroundColor: '#027be3',
						width: '4px',
						opacity: 1
					}"
					:vertical-bar-style="{
						right: '2px',
						borderRadius: '9px',
						backgroundColor: 'primary',
						width: '8px',
						opacity: 0.2
					}"
				>
					<div class="absolute fit column no-wrap q-ml-xs">
						<div
							v-for="n in orderListStatusProcessColumnCount"
							:key="n"
							class="col-6"
							:class="{'q-mb-xs': n !== orderListStatusProcessColumnCount}"
						>
							<!-- todo - stretch and make gutter -->
							<div class="row full-height">
								<q-card
									v-for="(order, index) in orderList[ORDER_STATUSES.PROCESS].slice(cardsInARow*n - cardsInARow, cardsInARow*n)"
									:key="index"
									class="col-2 full-height q-mr-xs flex flex-center text-white bg-green-9 cursor-pointer q-hoverable"
									@click="showOrderDetails(order)"
								>
									<span class="q-focus-helper"></span>
									<div class="text-center">
										#{{ order.id }}
									</div>
								</q-card>
							</div>
						</div>
					</div>
				</q-scroll-area>

			</div>
		</q-card>
		<q-card
			class="col bg-red-1"
		>
			<div class="column full-height">
				<div class="col-shrink">
					<div class="row q-py-sm">
						<q-chip
							square
							color="red-9"
							text-color="white"
						>
							{{ ORDER_STATUS_NAMES[ORDER_STATUSES.CANCEL] }}
						</q-chip>
					</div>
				</div>

				<!-- todo - visible scrollbar -->
				<q-scroll-area
					class="col full-height"
					visible
					:vertical-thumb-style="{
						right: '4px',
						borderRadius: '7px',
						backgroundColor: '#027be3',
						width: '4px',
						opacity: 1
					}"
					:vertical-bar-style="{
						right: '2px',
						borderRadius: '9px',
						backgroundColor: 'primary',
						width: '8px',
						opacity: 0.2
					}"
				>
					<div class="absolute fit column no-wrap q-ml-xs">
						<div
							v-for="n in orderListStatusCancelColumnCount"
							:key="n"
							class="col-6"
							:class="{'q-mb-xs': n !== orderListStatusCancelColumnCount}"
						>
							<!-- todo - stretch and make gutter -->
							<div class="row full-height">
								<q-card
									v-for="(order, index) in orderList[ORDER_STATUSES.CANCEL].slice(cardsInARow*n - cardsInARow, cardsInARow*n)"
									:key="index"
									class="col-2 full-height q-mr-xs flex flex-center text-white bg-red-9 cursor-pointer q-hoverable"
									@click="showOrderDetails(order)"
								>
									<span class="q-focus-helper"></span>
									<div class="text-center">
										#{{ order.id }}
									</div>
								</q-card>
							</div>
						</div>
					</div>
				</q-scroll-area>

			</div>
		</q-card>
		<q-card
			class="col bg-grey-1"
		>
			<div class="column full-height">
				<div class="col-shrink">
					<div class="row q-py-sm">
						<q-chip
							square
							color="grey-8"
							text-color="white"
						>
							{{ ORDER_STATUS_NAMES[ORDER_STATUSES.DONE] }}
						</q-chip>
					</div>
				</div>

				<!-- todo - visible scrollbar -->
				<q-scroll-area
					class="col full-height"
					visible
					:vertical-thumb-style="{
						right: '4px',
						borderRadius: '7px',
						backgroundColor: '#027be3',
						width: '4px',
						opacity: 1
					}"
					:vertical-bar-style="{
						right: '2px',
						borderRadius: '9px',
						backgroundColor: 'primary',
						width: '8px',
						opacity: 0.2
					}"
				>
					<div class="absolute fit column no-wrap q-ml-xs">
						<div
							v-for="n in orderListStatusDoneColumnCount"
							:key="n"
							class="col-6"
							:class="{'q-mb-xs': n !== orderListStatusDoneColumnCount}"
						>
							<!-- todo - stretch and make gutter -->
							<div class="row full-height">
								<q-card
									v-for="(order, index) in orderList[ORDER_STATUSES.DONE].slice(cardsInARow*n - cardsInARow, cardsInARow*n)"
									:key="index"
									class="col-2 full-height q-mr-xs flex flex-center text-white bg-grey-8 cursor-pointer q-hoverable"
									@click="showOrderDetails(order)"
								>
									<span class="q-focus-helper"></span>
									<div class="text-center">
										#{{ order.id }}
									</div>
								</q-card>
							</div>
						</div>
					</div>
				</q-scroll-area>

			</div>
		</q-card>
	</div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue"
import { api } from "src/boot/axios"
import { useRouter } from "vue-router"
import { Dialog } from "quasar"
import { ORDER_STATUSES, ORDER_STATUS_NAMES } from "src/const/orderStatuses"
import OrderCardDetailDialog from "src/components/dialogs/OrderCardDetailDialog.vue"

const props = defineProps({
	date: [String, Object]
})

const emit = defineEmits([
	"load"
])

const $router = useRouter()

const cardsInARow = 5

const orderList = ref({
	[ORDER_STATUSES.NEW]: [],
	[ORDER_STATUSES.PROCESS]: [],
	[ORDER_STATUSES.CANCEL]: [],
	[ORDER_STATUSES.DONE]: []
})

const orderListStatusNewColumnCount = computed(() =>
	Math.ceil(orderList.value[ORDER_STATUSES.NEW].length / cardsInARow)
)
const orderListStatusProcessColumnCount = computed(() =>
	Math.ceil(orderList.value[ORDER_STATUSES.PROCESS].length / cardsInARow)
)
const orderListStatusCancelColumnCount = computed(() =>
	Math.ceil(orderList.value[ORDER_STATUSES.CANCEL].length / cardsInARow)
)
const orderListStatusDoneColumnCount = computed(() =>
	Math.ceil(orderList.value[ORDER_STATUSES.DONE].length / cardsInARow)
)

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
