import { api } from "src/boot/axios"

export const login = async ({commit, state}, payload) => {
	const response = await api.post("auth", payload)
	commit("SET_USER", response.data.user)
	commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
	commit("SET_IS_LOGGED", true)

}

export const signUp = async ({commit}, payload) => {
	const response = await api.post("register", payload)
	api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token // todo - check if needed
	commit("SET_USER", response.data.user)
	commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
	commit("SET_IS_LOGGED", true)
}

export const logout = async ({commit, state}, payload) => {
	await api.post("personal/logout", payload)
	commit("SET_USER", {})
	commit("SWITCH_PERSONAL", "customer")
	commit("userProducer/EMPTY_USER_PRODUCER", {}, { root:true })
	commit("SET_IS_LOGGED", false)
}

export const authViaSession = async ({commit}) => {
	const response = await api.post("authViaSession")
	if (response.data) {
		commit("SET_USER", response.data.user)
		commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
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
	const response = await api.post("personal/register", {...payload, case: 1})
	commit("userProducer/SET_USER_PRODUCER", response.data.producer, { root:true })
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
