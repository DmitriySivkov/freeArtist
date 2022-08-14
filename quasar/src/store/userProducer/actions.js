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
