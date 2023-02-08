import { defineStore } from "pinia"
import { LocalStorage } from "quasar"
import { useNotification } from "src/composables/notification"

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
    setUserRequests(request) {
      if (this.user_requests.length > 0)
        this.user_requests = [...this.user_requests, Array.isArray(request) ? [...request] : request]
      else
        this.user_requests = Array.isArray(request) ? request : [request]
    },

    setUserTeamsRequests(request) {
      if (this.user_teams_requests.length > 0)
        this.user_teams_requests = [...this.user_teams_requests, Array.isArray(request) ? [...request] : request]
      else
        this.user_teams_requests = Array.isArray(request) ? request : [request]
    }
	}
})
