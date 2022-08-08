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
	commit("SET_USER_PRODUCER", response.data.producer)
	commit("SET_PRODUCER_ROLE", response.data.role)
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

export const getProducerIncomingRequests = async ({commit}, producerId) => {
	const response = await api.get(
		"personal/producers/relationRequests/incoming/" + producerId
	)
	commit("SET_PRODUCER_INCOMING_RELATION_REQUESTS", { ...response.data, producer_id: parseInt(producerId) })
}
