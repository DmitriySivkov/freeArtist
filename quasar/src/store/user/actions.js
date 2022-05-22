import { api } from "src/boot/axios"

export const login = async ({commit}, payload) => {
	const response = await api.post("auth", payload)
	commit("SET_USER", response.data)
	commit("SET_IS_LOGGED", true)
}

export const signUp = async ({commit}, payload) => {
	const response = await api.post("register", payload)
	commit("SET_USER", response.data)
	commit("SET_IS_LOGGED", true)
}

export const logout = async ({commit}, payload) => {
	await api.post("logout", payload)
	commit("SET_USER", {})
	commit("SET_IS_LOGGED", false)
}

export const checkTokenCookie = async ({commit}) => {
	const response = await api.post("hasTokenCookie")
	if (response.data) {
		commit("SET_USER", response.data)
		commit("SET_IS_LOGGED", true)
	}
}

export const verifyEmail = async ({commit}, payload) => {
	await api.post("auth/verify-email", {
		hash: payload.hash,
		email: payload.email
	})
}
