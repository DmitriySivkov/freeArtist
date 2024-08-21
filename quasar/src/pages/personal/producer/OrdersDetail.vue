<template>
	<div class="absolute column fit no-wrap">
		<div class="col">
			<div class="row justify-center full-height">
				<div class="col-xs-12 col-md-8 col-lg-7 col-xl-6">
					<!--					<ProducerOrderList />-->

					<!--					<navigation-bar-->
					<!--						@today="onToday"-->
					<!--						@prev="onPrev"-->
					<!--						@next="onNext"-->
					<!--					/>-->

					<div class="row justify-center">
						<div style="display: flex; max-width: 800px; width: 100%;">
							<q-calendar-task
								ref="calendar"
								v-model="selectedDate"
								v-model:model-tasks="parsedTasks"
								v-model:model-footer="footerTasks"
								view="month"
								:task-width="240"
								:min-weekday-length="2"
								:weekday-class="weekdayClass"
								:day-class="dayClass"
								:footer-day-class="footerDayClass"
								:focus-type="['weekday', 'date', 'task']"
								focusable
								hoverable
								animated
								bordered
								@change="onChange"
								@moved="onMoved"
								@click-date="onClickDate"
								@click-day="onClickDay"
								@click-head-day="onClickHeadDay"
							>
								<template #head-tasks="{ /* scope */ }">
									<div
										class="header ellipsis"
										style="font-weight: 600"
									>
										<div class="issue ellipsis">Issue</div>
										<div class="key">Key</div>
										<div class="logged">Logged</div>
									</div>
								</template>

								<template #task="{ scope }">
									<template
										v-for="task in getTasks(scope.start, scope.end, scope.task)"
										:key="task.key"
									>
										<div class="header ellipsis">
											<div class="issue ellipsis">
												<span
													v-if="scope.task.icon === 'done'"
													class="done"
												>
													done
												</span>
												<span
													v-else-if="scope.task.icon === 'pending'"
													class="pending"
												>
													pending
												</span>
												<span
													v-else-if="scope.task.icon === 'blocking'"
													class="blocking"
												>
													blocking
												</span>
												{{ scope.task.title }}
											</div>
											<div class="key">{{ scope.task.key }}</div>
											<div class="logged">{{ sum(scope.start, scope.end, scope.task) }}</div>
										</div>
									</template>
								</template>

								<template #day="{ scope }">
									<template
										v-for="time in getLogged(scope.timestamp.date, scope.task.logged)"
										:key="time"
									>
										<div class="logged-time">{{ time.logged }}</div>
									</template>
								</template>

								<template #footer-task="{ scope }">
									<div class="summary ellipsis">
										<div class="title ellipsis">{{ scope.footer.title }}</div>
										<div class="total">{{ totals(scope.start, scope.end) }}</div>
									</div>
								</template>

								<template #footer-day="{ scope }">
									<div class="logged-time">{{ getLoggedSummary(scope.timestamp.date) }}</div>
								</template>
							</q-calendar-task>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
// import ProducerOrderList from "src/components/orders/Producer/ProducerOrderList.vue"
import { ref, computed, onBeforeMount } from "vue"
import {
	QCalendarTask,
	today,
	isBetweenDates,
	parsed,
	padNumber
} from "@quasar/quasar-ui-qcalendar/src/index.js"
import "@quasar/quasar-ui-qcalendar/src/QCalendarVariables.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTransitions.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTask.sass"

const calendar = ref(null)

const selectedDate = ref(today())
const startDate = ref(today())
const endDate = ref(today())

const tasks = ref([
	{
		icon: "done",
		title: "Task 1",
		key: "TSK-584",
		logged: [
			{ date: "2021-03-02", logged: 0.5 },
			{ date: "2021-03-05", logged: 2.0 }
		]
	},
	{
		icon: "pending",
		title: "Task 2",
		key: "TSK-592",
		logged: [
			{ date: "2021-03-06", logged: 3.5 },
			{ date: "2021-03-08", logged: 4.0 }
		]
	},
	{
		icon: "pending",
		title: "Task 3",
		key: "TSK-593",
		logged: [
			{ date: "2021-03-10", logged: 4.5 },
			{ date: "2021-03-11", logged: 2.4 }
		]
	},
	{
		icon: "done",
		title: "Task 4",
		key: "TSK-594",
		logged: [
			{ date: "2021-03-14", logged: 6.5 },
			{ date: "2021-03-15", logged: 2.0 }
		]
	},
	{
		icon: "pending",
		title: "Task 5",
		key: "TSK-595",
		logged: [
			{ date: "2021-03-12", logged: 1.5 },
			{ date: "2021-03-18", logged: 2.0 }
		]
	},
	{
		icon: "blocking",
		title: "Task 6",
		key: "TSK-596",
		logged: [
			{ date: "2021-03-13", logged: 1.5 },
			{ date: "2021-03-23", logged: 3.5 }
		]
	},
	{
		icon: "pending",
		title: "Task 7",
		key: "TSK-597",
		logged: [
			{ date: "2021-03-19", logged: 0.75 },
			{ date: "2021-03-26", logged: 2.25 }
		]
	},
	{
		icon: "done",
		title: "Task 8",
		key: "TSK-598",
		logged: [
			{ date: "2021-03-21", logged: 1.5 },
			{ date: "2021-03-22", logged: 4.0 }
		]
	},
	{
		icon: "pending",
		title: "Task 9",
		key: "TSK-599",
		logged: [
			{ date: "2021-03-26", logged: 6.5 },
			{ date: "2021-03-27", logged: 3.5 }
		]
	},
	{
		icon: "blocking",
		title: "Task 10",
		key: "TSK-600",
		logged: [
			{ date: "2021-03-12", logged: 0.5 },
			{ date: "2021-03-14", logged: 2.0 },
			{ date: "2021-03-28", logged: 4.5 },
			{ date: "2021-03-30", logged: 1.0 }
		]
	}
])

const footerTasks = ref([
	{ title: "TOTALS" }
])

const parsedTasks = computed(() => {
	const start = parsed(startDate.value)
	const end = parsed(endDate.value)
	const taskArray = []

	for (let i = 0; i < tasks.value.length; ++i) {
		const task = tasks.value[i]
		for (let j = 0; j < task.logged.length; ++j) {
			const loggedTimestamp = parsed(task.logged[j].date)
			if (isBetweenDates(loggedTimestamp, start, end)) {
				taskArray.push(task)
				break
			}
		}
	}
	return taskArray
})

function getLogged (date, logged) {
	const val = []
	for (let index = 0; index < logged.length; ++index) {
		if (logged[ index ].date === date) {
			val.push({ logged: logged[ index ].logged })
			break
		}
	}
	return val
}

function getLoggedSummary (date) {
	let total = 0

	const reducer = (accumulator, currentValue) => {
		if (date === currentValue.date) {
			return accumulator + currentValue.logged
		}
		return accumulator
	}

	for (const index in tasks.value) {
		const task = tasks.value[ index ]
		total += task.logged.reduce(reducer, 0)
	}

	return total
}

/**
 * Sums up the amount of time spent on a task
 * This only sums it up if the logged date falls
 * between the start and end times
 */
function sum (start, end, task) {
	const reducer = (accumulator, currentValue) => {
		const loggedTimestamp = parsed(currentValue.date)
		if (isBetweenDates(loggedTimestamp, start, end)) {
			return accumulator + currentValue.logged
		}
		return accumulator
	}
	return task.logged.reduce(reducer, 0)
}

/**
 * Determines if the passed in task has logged time
 * between the start and end times
 */
function getTasks (start, end, task) {
	const tasks = []

	for (let index = 0; index < task.logged.length; ++index) {
		const loggedTimestamp = parsed(task.logged[ index ].date)
		if (isBetweenDates(loggedTimestamp, start, end)) {
			tasks.push(task)
			break
		}
	}
	return tasks
}

function weekdayClass (data) {
	return {
		"task__weekday--style": true
	}
}

function dayClass (data) {
	return {
		"task__day--style": true
	}
}

function footerDayClass (data) {
	return {
		"task__footer--day__style": true
	}
}

/**
 * Sums up the amount of time spent for all tasks
 * between the start and end dates
 */
function totals (start, end) {
	let total = 0
	const reducer = (accumulator, currentValue) => {
		const loggedTimestamp = parsed(currentValue.date)
		if (isBetweenDates(loggedTimestamp, start, end)) {
			return accumulator + currentValue.logged
		}
		return accumulator
	}

	for (const task in tasks.value) {
		total += tasks.value[ task ].logged.reduce(reducer, 0)
	}

	return total
}

function onToday () {
	calendar.value.moveToToday()
}

function onPrev () {
	calendar.value.prev()
}

function onNext () {
	calendar.value.next()
}
function onMoved (data) {
	console.log("onMoved", data)
}

function onChange (data) {
	console.log("onChange", data)
	startDate.value = data.start
	endDate.value  = data.end
}

function onClickDate (data) {
	console.log("onClickDate", data)
}

function onClickDay (data) {
	console.log("onClickDay", data)
}

function onClickHeadDay (data) {
	console.log("onClickHeadDay", data)
}

onBeforeMount(() => {
	// adjust all the dates for the current month
	const date = new Date()
	const year = date.getFullYear()
	const month = padNumber((date.getMonth() + 1), 2)
	tasks.value.forEach(task => {
		task.logged.forEach(logged => {
			// get last 2 digits from current date (day)
			const day = logged.date.slice(-2)
			logged.date = [ year, padNumber(month, 2), padNumber(day, 2) ].join("-")
		})
	})
})
</script>
