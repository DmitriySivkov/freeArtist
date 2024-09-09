import draggable from "vuedraggable"

// todo - когда в календарь добавят совместимость с @quasar/app-vite 2.0.0: https://github.com/quasarframework/quasar-ui-qcalendar/pull/436
// todo - изменить добавление плагина на расширение: quasar ext add @quasar/qcalendar@next
import QCalendarAgenda from "@quasar/quasar-ui-qcalendar/src/QCalendarAgenda.js"
import "@quasar/quasar-ui-qcalendar/src/QCalendarAgenda.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarVariables.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTransitions.sass"
import "@quasar/quasar-ui-qcalendar/src/QCalendarTask.sass"

export default (({ app }) => {
	app.component("draggable", draggable)

	app.use(QCalendarAgenda)
})
