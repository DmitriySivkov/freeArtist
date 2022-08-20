import { api } from "boot/axios"

export const getProducerIncomingRequests = async ({commit}, producerId) => {
	const response = await api.get(
		"personal/producers/relationRequests/incoming/" + producerId
	)
	commit("SET_PRODUCER_INCOMING_RELATION_REQUESTS", { ...response.data, producer_id: parseInt(producerId) })
}

export const acceptCoworkingRequest = async ({commit}, {producer_id, request_id}) => {
	const response = await api.post("personal/producers/relationRequests/acceptCoworkingRequest/" + request_id)
	commit("SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
		producer_id,
		request_id: response.data.id,
		status: response.data.status
	})
}

export const rejectCoworkingRequest = async ({commit}, {producer_id, request_id}) => {
	const response = await api.post("personal/producers/relationRequests/rejectCoworkingRequest/" + request_id)
	commit("SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
		producer_id,
		request_id: response.data.id,
		status: response.data.status
	})
}

export const getProducerUserList = async ({commit}, producer_id) => {
	const response = await api.get("personal/producers/" + producer_id + "/users")
	commit("SET_PRODUCER_USERS", {
		producer_id,
		users: response.data
	})
}

export const syncProducerUserPermissions = async ({commit}, { producer_id, user_id, permissions }) => {
	const response = await api.post(
		"personal/producers/permissions/" + producer_id + "/sync/" + user_id,
		permissions
	)
	commit("SYNC_PRODUCER_USER_PERMISSIONS", {
		producer_id,
		user_id,
		permissions:response.data
	})
}
