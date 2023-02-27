import { defineStore } from "pinia"

export const useRelationRequestStore = defineStore("relation_request", {
	state: () => ({
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
	}),

	actions: {
		commitUserRequest(request) {
			if (this.user_requests.length > 0)
				this.user_requests = [request, ...this.user_requests]
			else {
				this.user_requests = [request]
			}
		},

		setUserTeamsRequests(request) {
			if (this.user_teams_requests.length > 0)
				this.user_teams_requests = [...this.user_teams_requests, Array.isArray(request) ? [...request] : request]
			else
				this.user_teams_requests = Array.isArray(request) ? request : [request]
		},

		emptyUserRequests() {
			this.user_requests = []
			this.user_teams_requests = []
		},

		setUserRelationRequestStatus ({ request_id, status_id }) {
			this.user_requests.find((r) => r.id === request_id).status =
        Object.values(this.statuses).find((s) => s.id === status_id)
		},

		setUserTeamRelationRequestStatus({ request_id, status_id }) {
			this.user_teams_requests.find((r) => r.id === request_id).status =
        Object.values(this.statuses).find((s) => s.id === status_id)
		},

		removeUserCreatingRequest() {
			this.user_requests = this.user_requests.filter((r) => !r.is_creating_request)
		}
	}
})
