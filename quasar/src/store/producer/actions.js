import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("producers")
	commit("SET_PRODUCERS", response.data)
}

export const getProducer = async ({commit}, producerId) => {
	const response = await api.get("producers/" + producerId)
	commit("SET_CURRENT_PRODUCER", response.data)
}
