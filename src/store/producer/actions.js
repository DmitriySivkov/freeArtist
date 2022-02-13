import { api } from "boot/axios"

export const getList = async ({commit}) => {
	await api.get("producers").then((response) => {
		commit("SET_PRODUCERS", response.data)
	})
}
