import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request"
import { LocalStorage } from "quasar"
import { useRoleStore } from "src/stores/role"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user", // todo - make const file for personal tab names
		location: null,
		location_range: null,
		data: {}
	}),

	actions: {
		async registerProducer(payload) {
			const team_store = useTeamStore()
			const role_store = useRoleStore()

			const response = await api.post("personal/producers/register", { ...payload })

			team_store.setUserTeams(response.data.team)

			role_store.setUserRole(response.data.role)
		},

		switchPersonal(personal_tab) {
			this.personal_tab = personal_tab
			LocalStorage.set("personal_tab", personal_tab)
		},

		async createRequest({team, message}) {
			return api.post(
				"personal/users/relationRequests/" + team.value + "/create",
				{ message }
			)
		},

		async setRelationRequestStatus({ request_id, status_id }) {
			const relation_request_store = useRelationRequestStore()

			const response = await api.post(
				"personal/users/relationRequests/" + request_id + "/setStatus",
				{ status_id }
			)

			relation_request_store.setUserRelationRequestStatus({
				request_id: response.data.id,
				status_id: response.data.status.id
			})
		},

		setLocation(location) {
			this.location = location

			LocalStorage.set("location", location)
		},

		setLocationRange(range) {
			this.location_range = range
		},

		setIsLogged(isLogged) {
			this.is_logged = isLogged
		},

		setData(data) {
			this.data = data
		}
	}
})
