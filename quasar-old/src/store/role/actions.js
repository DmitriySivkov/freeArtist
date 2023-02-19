import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("roles")
	commit("SET_ROLES", response.data)
}
