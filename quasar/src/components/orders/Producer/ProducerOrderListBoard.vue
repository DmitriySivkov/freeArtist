<template>
	<q-card
		class="col q-px-xs"
		:class="boardClass"
	>
		<div class="column full-height q-pb-xs">
			<div class="col-auto">
				<div class="row q-py-xs">
					<div class="col">
						<q-btn
							no-caps
							:color="chipColor"
							:label="boardName"
							:loading="isMounting"
							class="cursor-inherit"
						/>
					</div>
					<div
						v-if="withCalendar"
						class="col-shrink"
					>
						<q-btn
							:color="chipColor"
							icon="date_range"
							:loading="loadingBoard === boardId"
							@click="showCalendarDialog"
						/>
					</div>
				</div>
			</div>

			<div
				class="col"
				:class="draggableContainerClass"
			>
				<draggable
					:list="orders"
					group="orders"
					@start="drag=true"
					@end="onEnd"
					:sort="isSortable"
					:item-key="(order) => order.id"
					class="row full-height q-gutter-xs"
					:component-data="{
						type: 'transition-group',
						name: 'fade',
						id: boardId
					}"
					v-bind="{
						animation: 200,
						group: 'orders',
						disabled: false,
						ghostClass,
						delay: 700,
						delayOnTouchOnly: true
					}"
				>
					<template #item="{ element }">
						<q-card
							class="col-3 flex flex-center cursor-pointer q-hoverable"
							:class="cardClass"
							@click="$emit('show', element)"
							style="height:45%"
						>
							<span class="q-focus-helper"></span>
							<div class="text-center">
								#{{ element.id }}
							</div>
						</q-card>
					</template>
				</draggable>
			</div>
		</div>
	</q-card>
</template>

<script setup>
import { ref } from "vue"
import { date, Dialog} from "quasar"
import draggable from "vuedraggable"
import ProducerOrderListCalendarDialog from "src/components/dialogs/ProducerOrderListCalendarDialog.vue"
// todo - gutter height is unequal when 2 rows of orders
const emit = defineEmits([
	"show",
	"change",
	"calendar"
])

const onEnd = (e) => {
	drag.value = false

	const statusId = parseInt(e.to.getAttribute("id"))

	// if dropped to the same board & the same position
	if (
		props.boardId === statusId &&
		e.oldIndex === e.newIndex
	) return

	emit("change")
}

const props = defineProps({
	boardId: Number,
	boardName: String,
	orders: Array,
	boardClass: String,
	cardClass: String,
	chipColor: String,
	isMounting: Boolean,
	ghostClass: String,
	draggableContainerClass: String,
	withCalendar: {
		type: Boolean,
		default: false
	},
	loadingBoard: {
		type: Number,
		required: false
	},
	isSortable: {
		type: Boolean,
		default: false
	}
})

const drag = ref(false)

const calendarDate = ref(
	date.formatDate(Date.now(), "DD.MM.YYYY")
)

const showCalendarDialog = () => {
	Dialog.create({
		component: ProducerOrderListCalendarDialog,
		componentProps: {
			date: calendarDate.value,
			calendarColor: props.chipColor
		}
	}).onOk((date) => {
		calendarDate.value = date

		emit("calendar", date)
	})
}
</script>
