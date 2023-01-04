import { api } from "src/boot/axios"

export const login = async ({commit, state}, payload) => {
	const response = await api.post("auth", payload)

	if (response.data.token)
		api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

	commit("SET_USER", response.data.user)
	commit("team/SET_USER_TEAMS", response.data.user_teams, { root:true })
	commit("relation_request/SET_USER_REQUESTS", response.data.user_requests, { root:true })
	commit("relation_request/SET_USER_TEAMS_REQUESTS", response.data.user_teams_requests, { root:true })
	commit("SET_IS_LOGGED", true)

	return response
}

export const signUp = async ({commit}, payload) => {
	const response = await api.post("register", payload)

	if (response.data.token)
		api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

	commit("SET_USER", response.data.user)
	commit("SET_IS_LOGGED", true)

	return response
}

export const logout = async ({commit, state}, payload) => {
	await api.post("personal/logout", payload)

	commit("SET_USER", {})
	commit("SWITCH_PERSONAL", "user")
	commit("team/EMPTY_USER_TEAMS", {}, { root:true })
	commit("relation_request/EMPTY_USER_REQUESTS", {}, { root:true })
	commit("SET_IS_LOGGED", false)
}

export const authViaToken = async ({commit}, { token }) => {
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
}

export const verifyEmail = async ({commit}, payload) => {
	await api.post("auth/verify-email", {
		hash: payload.hash,
		email: payload.email
	})
}

export const registerProducer = async ({commit}, payload) => {
	const response = await api.post("personal/producers/register", { ...payload })
	commit("team/SET_USER_TEAMS", response.data.team, { root:true })
	commit("SET_ROLE", response.data.role)
}

export const switchPersonal = async ({commit}, personal_tab) => {
	commit("SWITCH_PERSONAL", personal_tab)
}

export const sendCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/sendCoworkingRequest", { ...payload })
	commit("SET_USER_OUTGOING_COWORKING_REQUESTS", response.data)
}

export const cancelCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/cancelCoworkingRequest/" + payload.requestId)
	commit("CANCEL_USER_OUTGOING_COWORKING_REQUEST", {
		canceledRequest: response.data,
		canceledStatus: payload.status
	})
}

export const restoreCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/restoreCoworkingRequest/" + payload.requestId)
	commit("RESTORE_USER_OUTGOING_COWORKING_REQUEST", {
		restoredRequest: response.data,
		restoredStatus: payload.status
	})
}
