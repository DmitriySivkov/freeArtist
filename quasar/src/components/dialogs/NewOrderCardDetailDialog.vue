<script setup>
import { ref, computed } from "vue"
import { useDialogPluginComponent } from "quasar"
import { ORDER_STATUS_NAMES, ORDER_CARD_STATUS_TO_CLASS, ORDER_STATUSES } from "src/const/orderStatuses"
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { api } from "src/boot/axios"
import { useScreen } from "src/composables/screen"

defineEmits([
	...useDialogPluginComponent.emits,
])

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

const props = defineProps({
	order: Object,
})

const { isSmallScreen } = useScreen()

const statusOptions = computed(() =>
	Object.values(ORDER_STATUSES).map((statusId) => ({
		id: statusId,
		label: ORDER_STATUS_NAMES[statusId]
	}))
)

const assigneeOptions = computed(() =>
	props.assignees.map((a) => ({
		id: a.id,
		label: a.name ?? a.phone
	}))
)

const producerOrderStore = useProducerOrdersStore()

const isLoading = ref(false)

const changeStatus = (status) => {
	isLoading.value = true

	const promise = api.post(`personal/producers/${props.order.producer_id}/orders/${props.order.id}`, {
		status: status.id,
		_method: "PUT"
	})

	promise.then(() => {
		let order = {...props.order, status}

		producerOrderStore.commitData([order])

		onDialogOK()
	})

	//todo
	promise.catch()

	promise.finally(() => isLoading.value = false)
}
</script>

<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
		:maximized="isSmallScreen"
		transition-show="slide-up"
		transition-hide="slide-down"
	>
		<q-card
			class="q-dialog-plugin text-body1"
			:class="ORDER_CARD_STATUS_TO_CLASS[ORDER_STATUSES.NEW]"
		>
			<q-card-section class="q-py-sm">
				<div class="text-right">
					<q-icon
						v-close-popup
						name="close"
						size="md"
						class="cursor-pointer"
					/>
				</div>

				<span> Статус </span>
				<q-select
					dense
					filled
					:model-value="order.status"
					:options="statusOptions"
					option-value="id"
					option-label="label"
					class="q-mb-md"
					input-class="text-white"
					@update:model-value="changeStatus"
				>
					<template #selected-item>
						<span>{{ ORDER_STATUS_NAMES[order.status] }}</span>
					</template>
				</q-select>

				<span>Создан</span>
				<q-field
					dense
					filled
					readonly
					class="q-mb-md"
				>
					<div class="self-center">
						<span class="text-black">{{ order.created_at }}</span>
					</div>
				</q-field>

				<span>Когда приготовить</span>
				<q-field
					dense
					filled
					readonly
					class="q-mb-md"
				>
					<div class="self-center">
						<span class="text-black">{{ order.prepare_by_formatted }}</span>
					</div>
				</q-field>
			</q-card-section>
			<q-separator />
			<q-card-section>
				<div class="row justify-between q-mb-md">
					<div class="col">#{{ order.producer_order_id }}</div>
					<div class="col text-right">для {{ order.user }}</div>
				</div>
				<div
					v-for="product in order.products"
					:key="product.product_id"
					class="row q-py-md justify-center"
				>
					<div class="col">
						{{ product.title }}
					</div>
					<div class="col-auto text-right">
						{{ order.order_products[product.id] }} шт
					</div>
					<q-separator class="full-width" />
				</div>
			</q-card-section>

			<!-- todo - если размер окна больше размера экрана, то q-inner-loading не расстягивается на всю длину блока, а только на величину экрана -->
			<q-inner-loading :showing="isLoading">
				<q-spinner-gears
					size="lg"
					color="primary"
				/>
			</q-inner-loading>
		</q-card>
	</q-dialog>
</template>
