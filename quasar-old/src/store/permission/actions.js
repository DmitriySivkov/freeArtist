import { api } from "boot/axios"

export const getList = async ({commit}) => {
	const response = await api.get("permissions")
	commit("SET_PERMISSIONS", response.data)
}
