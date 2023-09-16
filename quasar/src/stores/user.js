import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request"
import { LocalStorage } from "quasar"
import { usePermissionStore } from "src/stores/permission"
import { useRoleStore } from "src/stores/role"

export const useUserStore = defineStore("user", {
	state: () => ({
		is_logged: false,
		personal_tab: "user",
		location: null,
		location_range: null,
		data: {}
	}),

	actions: {
		async login(payload) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()
			const permission_store = usePermissionStore()
			const role_store = useRoleStore()

			const response = await api.post("auth", payload)

			if (response.data.token)
				api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

			this.data = response.data.user

			role_store.setUserRoles(response.data.user_roles)
			permission_store.setUserPermissions(response.data.user_permissions)

			team_store.setUserTeams(response.data.user_teams)

			if (response.data.user_requests.length > 0)
				relation_request_store.commitUserRequest(response.data.user_requests)

			if (response.data.user_teams_requests.length > 0)
				relation_request_store.setUserTeamsRequests(response.data.user_teams_requests)

			return response
		},

		async signUp(payload) {
			const response = await api.post("register", payload)

			if (response.data.token)
				api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

			this.data = response.data.user
			this.setIsLogged(true)

			return response
		},

		// todo - try to make synchronous request with promise
		async logout(payload) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()

			await api.post("personal/logout", payload)

			this.data = {}

			this.switchPersonal("user")

			team_store.emptyUserTeams()
			relation_request_store.emptyUserRequests()

			this.setIsLogged(false)
		},

		async authViaToken({ token }) {
			const team_store = useTeamStore()
			const relation_request_store = useRelationRequestStore()
			const permission_store = usePermissionStore()
			const role_store = useRoleStore()

			if (token && token.value)
				api.defaults.headers.common["Authorization"] = "Bearer " + token.value

			const response = await api.post("authViaToken")

			if (response.data) {
				this.data = response.data.user

				role_store.setUserRoles(response.data.user_roles)
				permission_store.setUserPermissions(response.data.user_permissions)

				team_store.setUserTeams(response.data.user_teams)

				if (response.data.user_requests.length > 0)
					relation_request_store.commitUserRequest(response.data.user_requests)

				if (response.data.user_teams_requests.length > 0)
					relation_request_store.setUserTeamsRequests(response.data.user_teams_requests)

				this.setIsLogged(true)
			}
		},

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
	}
})
