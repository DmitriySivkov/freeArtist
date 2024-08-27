<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card
			class="q-dialog-plugin text-body1"
			:class="cardClass"
		>
			<q-card-section class="q-py-sm">

				<div class="q-mb-md">#{{ order.id }}</div>

				<q-select
					dense
					filled
					:model-value="order.status"
					:display-value="ORDER_STATUS_NAMES[order.status]"
					:options="statusOptions"
					option-value="id"
					option-label="label"
					class="q-mb-md"
					:loading="isLoading"
					:disable="isLoading"
					input-class="text-white"
					@update:model-value="changeStatus"
				>
					<template #selected-item="{opt}">
						<span :class="{'text-white': opt !== ORDER_STATUSES.NEW}">
							{{ ORDER_STATUS_NAMES[opt] }}
						</span>
					</template>
				</q-select>
				<div>
					Создан: {{ order.created_at }}
				</div>
			</q-card-section>
			<q-separator />
			<q-card-section>
				<div class="text-right">
					для {{ order.user }}
				</div>
				<div
					v-for="product in order.products"
					:key="product.product_id"
					class="row q-py-md justify-center"
				>
					<div class="col-xs-12 col-sm-11">
						<div class="row">
							<div class="col">
								{{ product.title }}
							</div>
							<div class="col-shrink text-right">
								{{ order.order_products[product.id] }} шт
							</div>
						</div>
						<q-separator />
					</div>
				</div>
			</q-card-section>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { ref, computed } from "vue"
import { useDialogPluginComponent } from "quasar"
import { ORDER_STATUS_NAMES, ORDER_CARD_STATUS_TO_CLASS, ORDER_STATUSES } from "src/const/orderStatuses"
import { useProducerOrdersStore } from "src/stores/producerOrders"
import { api } from "src/boot/axios"

defineEmits([
	...useDialogPluginComponent.emits,
])

const props = defineProps({
	order: Object,
})

const cardClass = computed(() => ORDER_CARD_STATUS_TO_CLASS[props.order.status])

const statusOptions = computed(() =>
	Object.values(ORDER_STATUSES).map((statusId) => ({
		id: statusId,
		label: ORDER_STATUS_NAMES[statusId]
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

	promise.then((response) => {
		producerOrderStore.commitOrderFields({
			orderId: response.data.id,
			fields: {
				status: response.data.status
			},
		})
	})

	//todo
	promise.catch()

	promise.finally(() => isLoading.value = false)
}

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()
</script>
