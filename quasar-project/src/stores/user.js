import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request"
import { LocalStorage } from "quasar"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user",
		location: null,
		location_range: 1,
		data: {}
	}),

	actions: {
		async login(payload) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()

			const response = await api.post("auth", payload)

			if (response.data.token)
				api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

			this.data = response.data.user

			team_store.setUserTeams(response.data.user_teams)

			relation_request_store.setUserRequests(response.data.user_requests)
			relation_request_store.setUserTeamsRequests(response.data.user_teams_requests)

			this.is_logged = true

			return response
		},

		async signUp(payload) {
			const response = await api.post("register", payload)

			if (response.data.token)
				api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

			this.data = response.data.user
			this.is_logged = true

			return response
		},

		async logout(payload) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()

			await api.post("personal/logout", payload)

			this.data = {}

			this.switchPersonal("user")

			team_store.emptyUserTeams()
			relation_request_store.emptyUserRequests()

			this.is_logged = false
		},

		async authViaToken({ token }) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()

			if (token && token.value)
				api.defaults.headers.common["Authorization"] = "Bearer " + token.value

			const response = await api.post("authViaToken")

			if (response.data) {
				this.data = response.data.user
				team_store.setUserTeams(response.data.user_teams)

				relation_request_store.setUserRequests(response.data.user_requests)
				relation_request_store.setUserTeamsRequests(response.data.user_teams_requests)

				this.is_logged = true
			}
		},

		async registerProducer(payload) {
			const team_store = useTeamStore()

			const response = await api.post("personal/producers/register", { ...payload })

			team_store.setUserTeams(response.data.team)

			this.setRole(response.data.role)
		},

		switchPersonal(personal_tab) {
			this.personal_tab = personal_tab
			LocalStorage.set("personal_tab", personal_tab)
		},

		async createRequest(payload) {
			const relation_request_store = useRelationRequestStore()

			const response = await api.post("personal/users/relationRequests/create", { ...payload })

			relation_request_store.setUserRequests(response.data)
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

		async setLocation() {
			const response = await api.get("user/location")
			this.location = response.data
		},

		setRole(role) {
			if (this.data.roles.find((r) => r.id === role.id))
				return

			this.data.roles = [...this.data.roles, role]
		},

		setLocationRange(range) {
			this.location_range = range
		}
	}
})