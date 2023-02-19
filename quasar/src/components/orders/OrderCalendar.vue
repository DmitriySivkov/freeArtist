<template>
	<q-date
		@update:model-value="showOrders($event)"
		v-model="qdate"
		mask="DD.MM.YYYY"
		:locale="ruLocale"
		style="width:100%"
	/>
</template>

<script>
import { date } from "quasar"
import { ref } from "vue"
import { useOrderStore } from "src/stores/order"
export default {
	setup () {
		const order_store = useOrderStore()
		const qdate = ref(date.formatDate(Date.now(), "DD.MM.YYYY"))
		const ruLocale = {
			days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
			daysShort: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
			months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			monthsShort: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			firstDayOfWeek: 1
		}

		const showOrders = (date) => {
			if (date === null) return

			order_store.getList({
				filter: { date }
			})
		}

		return {
			qdate,
			ruLocale,
			showOrders
		}
	}
}
</script>
