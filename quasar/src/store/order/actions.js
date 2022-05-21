import { api } from "src/boot/axios"

export const getList = async ({commit}, payload) => {
	const response = await api.get("orders", {
		params: payload
	})

	commit("SET_ORDER_LIST", response.data)
}

export const create = async ({commit}, payload) => {
	const response = await api.post("orders", {
		cart: payload
	})
}
