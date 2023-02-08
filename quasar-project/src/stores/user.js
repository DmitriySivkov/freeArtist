import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request"

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

      commit("SET_USER", response.data.user)
      commit("SET_IS_LOGGED", true)

      return response
    },

    async logout(payload) {
      await api.post("personal/logout", payload)

      commit("SET_USER", {})
      commit("SWITCH_PERSONAL", "user")
      commit("team/EMPTY_USER_TEAMS", {}, { root:true })
      commit("relation_request/EMPTY_USER_REQUESTS", {}, { root:true })
      commit("SET_IS_LOGGED", false)
    },

    async authViaToken({ token }) {
      if (token && token.value)
        api.defaults.headers.common["Authorization"] = "Bearer " + token.value

      const response = await api.post("authViaToken")

      if (response.data) {
        commit("SET_USER", response.data.user)
        commit("team/SET_USER_TEAMS", response.data.user_teams, { root:true })
        commit("relation_request/SET_USER_REQUESTS", response.data.user_requests, { root:true })
        commit("relation_request/SET_USER_TEAMS_REQUESTS", response.data.user_teams_requests, { root:true })
        commit("SET_IS_LOGGED", true)
      }
    },

    async verifyEmail(payload) {
      await api.post("auth/verify-email", {
        hash: payload.hash,
        email: payload.email
      })
    },

    async registerProducer(payload) {
      const response = await api.post("personal/producers/register", { ...payload })
      commit("team/SET_USER_TEAMS", response.data.team, { root:true })
      commit("SET_ROLE", response.data.role)
    },

    switchPersonal(personal_tab) {
      commit("SWITCH_PERSONAL", personal_tab)
    },

    async createRequest(payload) {
      const response = await api.post("personal/users/relationRequests/create", { ...payload })
      commit("relation_request/SET_USER_REQUESTS", response.data, { root:true })
    },

    async setRelationRequestStatus({ request_id, status_id }) {
      const response = await api.post(
        "personal/users/relationRequests/" + request_id + "/setStatus",
        { status_id }
      )
      commit("relation_request/SET_USER_RELATION_REQUEST_STATUS", {
        request_id: response.data.id,
        status_id: response.data.status.id
      }, { root:true })
    },

    async setLocation() {
      const response = await api.get("user/location")
      commit("SET_LOCATION", response.data)
    }
	}
})
