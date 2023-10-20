<template>
	<q-date
		@update:model-value="$emit('update:modelValue', $event)"
		:model-value="modelValue"
		mask="DD.MM.YYYY"
		:locale="ruLocale"
		no-unset
		style="width:100%"
	/>
</template>

<script setup>
defineProps({
	modelValue: {
		type: String,
		required: false
	},
})

const ruLocale = {
	days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
	daysShort: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
	months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	monthsShort: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
	firstDayOfWeek: 1
}
</script>
