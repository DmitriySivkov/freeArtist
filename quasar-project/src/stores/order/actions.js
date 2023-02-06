import { api } from "src/boot/axios"

export const getList = async ({commit}, payload) => {
	const response = await api.get("personal/orders", {
		params: payload
	})

	commit("SET_ORDER_LIST", response.data)
}

export const create = async ({commit}, payload) => {
	const response = await api.post("personal/orders", {
		cart: payload
	})
}
