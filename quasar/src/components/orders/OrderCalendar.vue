<template>
	<q-date
		@update:model-value="this.showOrders($event)"
		v-model="this.qdate"
		mask="DD.MM.YYYY"
		:locale="this.ruLocale"
		style="width:100%"
	/>
</template>

<script>
import { date } from "quasar"
import { computed, ref } from "vue"
import { useStore } from "vuex"

export default {
	setup () {
		const $store = useStore()
		const qdate = ref(date.formatDate(Date.now(), "DD.MM.YYYY"))
		const ruLocale = {
			days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"],
			daysShort: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
			months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			monthsShort: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
			firstDayOfWeek: 1
		}

		const user = computed(() => $store.state.user.data)

		$store.dispatch("order/getList", {
			filter: {date: qdate.value, user: user.value}
		})

		return {
			qdate,
			ruLocale,

			showOrders(date) {
				$store.dispatch("order/getList", {
					filter: { date, user: user.value }
				})
			}
		}
	}
}
</script>
