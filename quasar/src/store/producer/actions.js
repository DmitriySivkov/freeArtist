import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("producers")
	commit("SET_PRODUCERS", response.data.data)
}
