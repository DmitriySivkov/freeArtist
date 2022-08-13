import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("producers")
	commit("SET_PRODUCERS", response.data)
}

export const getProducer = async ({commit}, producerId) => {
	const response = await api.get("producers/" + producerId)
	commit("SET_CURRENT_PRODUCER", response.data)
}

export const sendProducerPartnershipRequest = async ({commit}, payload) => {
	const response = await api.post(
		"personal/producers/relationRequests/sendProducerPartnershipRequest/" + payload.ownProducerId,
		{ ...payload }
	)
	commit("SET_PRODUCER_OUTGOING_PRODUCER_PARTNERSHIP_REQUESTS", response.data)
}
