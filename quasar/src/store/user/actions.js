import { api } from "src/boot/axios"

export const login = async ({commit}, payload) => {
	const response = await api.post("auth", payload, {headers: {"X-APP-TYPE":"web-app"}})
	console.log(response.errors)
	commit("SET_USER", response.data.data)
	commit("SET_IS_LOGGED", true)
}

export const signUp = async ({commit}, payload) => {
	await api.post("register", payload, {headers: {"X-APP-TYPE":"web-app"}})
}

export const logout = async ({commit}, payload) => {
	await api.post("logout", payload)
	commit("SET_USER", {})
	commit("SET_IS_LOGGED", false)
}

export const checkTokenCookie = async () => {
	await api.post("hasTokenCookie")
}

export const verifyEmail = async ({commit}, payload) => {
	await api.post("auth/verify-email", {
		hash: payload.hash,
		email: payload.email
	})
}
