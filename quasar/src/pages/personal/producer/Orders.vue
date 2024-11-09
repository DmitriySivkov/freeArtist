<script setup>
import ProducerOrdersWeekly from "src/components/producers/ProducerOrdersWeekly.vue"
import ProducerOrdersMonthly from "src/components/producers/ProducerOrdersMonthly.vue"
import PersonalProducerOrdersFooter from "src/layouts/PersonalProducerOrdersFooter.vue"
import { ref, computed, onBeforeUnmount } from "vue"
import { usePrivateChannels } from "src/composables/privateChannels"
import { useUserStore } from "src/stores/user"
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { useRouter } from "vue-router"
import { api } from "src/boot/axios"

const $router = useRouter()

const userStore = useUserStore()
const producerOrdersStore = useProducerOrdersStore()

const team = computed(() =>
	userStore.teams.find((t) =>
		t.detailed_id === parseInt($router.currentRoute.value.params.producer_id)
	)
)

const isWeeklyView = ref(true)

const isInitializing = ref(true)

const newOrders = ref([])
const assignees = ref([])

const isLoading = ref(true)

const loadOrders = (dateRange) => {
	isLoading.value = true

	const promise = api.get(`personal/producers/${team.value.detailed_id}/orders`,{
		params: {
			dateRange,
			isInitializing: isInitializing.value ? 1 : 0
		}
	})

	// todo - catch
	promise.then((response) => {
		if (!isInitializing.value) {
			producerOrdersStore.commitData(response.data)
		} else {
			producerOrdersStore.commitData(response.data.orders)
			assignees.value = response.data.assignees
			newOrders.value = response.data.new_orders
		}
	})

	promise.finally(() => {
		isLoading.value = false

		if (isInitializing.value) {
			isInitializing.value = false
		}
	})
}

const acceptOrder = (orderId) => {
	newOrders.value = newOrders.value.filter((o) => o.id !== orderId)
}

/** is triggered on component mount **/
const calendarChanged = ({ start, end }) => {
	producerOrdersStore.emptyData()
	loadOrders({ start, end })
}

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
		<ProducerOrdersWeekly
			v-if="isWeeklyView && team"
			:is-loading="isLoading"
			:is-initializing="isInitializing"
			:assignees="assignees"
			:new-orders="newOrders"
			@initialized="isInitializing = false"
			@change="calendarChanged"
			@accept-order="acceptOrder"
		/>
		<ProducerOrdersMonthly
			v-else
			@change="calendarChanged"
		/>
	</q-page>

	<PersonalProducerOrdersFooter
		v-model="isWeeklyView"
		:is-loading="isInitializing"
		@showMonthlyView="() => false"
		@showWeeklyView="() => false"
	/>
</template>

<style lang="scss">
.orders-current-day {
	font-weight: normal !important;
}

.orders-current-day .q-calendar__button {
	color: #606c71 !important;
	background-color: white !important;
	border-color: transparent !important;
}

.orders-not-current-day {
	font-weight: normal !important;
}

.orders-not-current-day .q-calendar__button {
	border-color: #d74720 !important;
	background: none !important;
}

.q-calendar-agenda__pane {
	height: 100%
}

.q-calendar-agenda__head--day.q-column-day {
	background-color: $secondary;
	color: white;
	align-content: center;
}
</style>
