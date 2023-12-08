<template>
	<q-dialog
		ref="dialogRef"
		@hide="onDialogHide"
	>
		<q-card class="q-dialog-plugin">
			<q-date
				@update:model-value="onDialogOK($event)"
				:model-value="date"
				mask="DD.MM.YYYY"
				:locale="ruLocale"
				no-unset
				minimal
				range
				style="width:100%"
				:color="calendarColor"
			/>
		</q-card>
	</q-dialog>
</template>

<script setup>
import { useDialogPluginComponent } from "quasar"

const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent()

defineEmits([
	...useDialogPluginComponent.emits,
])

defineProps({
	date: [String, Object],
	calendarColor: String
})

const ruLocale = {
	days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
	daysShort: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
	months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	monthsShort: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	firstDayOfWeek: 1
}
</script>
