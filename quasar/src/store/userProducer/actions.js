import { api } from "boot/axios"

export const getProducerIncomingRequests = async ({commit}, producerId) => {
	const response = await api.get(
		"personal/producers/relationRequests/incoming/" + producerId
	)
	commit("SET_PRODUCER_INCOMING_RELATION_REQUESTS", { ...response.data, producer_id: parseInt(producerId) })
}

export const acceptCoworkingRequest = async ({commit}, requestId) => {
	const response = await api.post("personal/producers/relationRequests/acceptCoworkingRequest/" + requestId)
}

export const rejectCoworkingRequest = async ({commit}, requestId) => {
	const response = await api.post("personal/producers/relationRequests/rejectCoworkingRequest/" + requestId)
}
