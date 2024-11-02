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
	assignees: Array
})

const { isSmallScreen } = useScreen()

const cardClass = computed(() => ORDER_CARD_STATUS_TO_CLASS[props.order.status])

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

const loadingField = ref(null)

const update = ({ field, value }) => {
	loadingField.value = field

	const promise = api.post(`personal/producers/${props.order.producer_id}/orders/${props.order.id}`, {
		[field]: value,
		_method: "PUT"
	})

	promise.then((response) => {
		producerOrderStore.commitOrderFields({
			orderId: response.data.id,
			fields: {
				...response.data
			},
		})
	})

	//todo
	promise.catch()

	promise.finally(() => loadingField.value = null)
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
			:class="cardClass"
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

				<template v-if="assignees.length !== 1">
					<span> Исполнитель </span>
					<q-select
						dense
						filled
						:model-value="order.assignee.id"
						:options="assigneeOptions"
						option-value="id"
						option-label="label"
						class="q-mb-md"
						:loading="loadingField === 'assignee_id'"
						:disable="loadingField === 'assignee_id'"
						input-class="text-white"
						@update:model-value="update({
							field: 'assignee_id',
							value: $event.id
						})"
					>
						<template #selected-item>
							<span :class="{'text-white': order.status !== ORDER_STATUSES.NEW}">
								{{ order.assignee.name ?? order.assignee.phone }}
							</span>
						</template>
					</q-select>
				</template>
				<span> Статус </span>
				<q-select
					dense
					filled
					:model-value="order.status"
					:options="statusOptions"
					option-value="id"
					option-label="label"
					class="q-mb-md"
					:loading="loadingField === 'status'"
					:disable="loadingField === 'status'"
					input-class="text-white"
					@update:model-value="update({
						field: 'status',
						value: $event.id
					})"
				>
					<template #selected-item>
						<span :class="{'text-white': order.status !== ORDER_STATUSES.NEW}">
							{{ ORDER_STATUS_NAMES[order.status] }}
						</span>
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
						<span :class="order.status !== ORDER_STATUSES.NEW ? 'text-white' : 'text-black'">
							{{ order.created_at }}
						</span>
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
		</q-card>
	</q-dialog>
</template>
