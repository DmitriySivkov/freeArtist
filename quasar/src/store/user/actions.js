import { api } from "src/boot/axios"

export const login = async ({commit}, payload) => {
	const response = await api.post("auth", payload)
	commit("SET_USER", response.data)
	commit("SET_IS_LOGGED", true)
}

export const signUp = async ({commit}, payload) => {
	const response = await api.post("register", payload)
	commit("SET_USER", response.data)
	commit("SET_IS_LOGGED", true)
}

export const logout = async ({commit}, payload) => {
	await api.post("personal/logout", payload)
	commit("SET_USER", {})
	commit("SET_IS_LOGGED", false)
}

export const checkTokenCookie = async ({commit}) => {
	const response = await api.post("hasTokenCookie")
	if (response.data) {
		commit("SET_USER", response.data)
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
	commit("SET_USER_PRODUCER", ...response.data.producers)
}

export const sendCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/sendCoworkingRequest", { ...payload })
	commit("SET_USER_OUTGOING_COWORKING_REQUESTS", response.data)
}

export const getCoworkingRequests = async ({commit}, payload) => {
	const response = await api.get("personal/getCoworkingRequests")
	commit("SET_USER_OUTGOING_COWORKING_REQUESTS", response.data)
}

export const cancelCoworkingRequest = async ({commit}, requestId) => {
	const response = await api.post("personal/users/relationRequests/cancelCoworkingRequest/" + requestId)
	commit("CANCEL_USER_OUTGOING_COWORKING_REQUEST", response.data)
}
