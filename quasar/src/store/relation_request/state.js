export default {
	user_requests: [],
	user_teams_requests: [],
	statuses: {
		pending: {
			id: 1,
			label: "На рассмотрении"
		},
		accepted: {
			id: 2,
			label: "Принято"
		},
		rejected_by_recipient: {
			id: 3,
			label: "Отказано получателем"
		},
		rejected_by_contributor: {
			id: 4,
			label: "Отменено заявителем"
		}
	},
	types: {
		coworking: {
			id: 1,
			label: "Совместная деятельность"
		},
		producer_partnership: {
			id: 2,
			label: "Партнерство изготовителей"
		}
	}
}
