import { api } from "src/boot/axios"
import { echo } from "src/boot/ws"
import { useUserProducer } from "src/composables/userProducer"
import { useUserPermission } from "src/composables/userPermission"

export const login = async ({commit, state}, payload) => {
	const response = await api.post("auth", payload)
	commit("SET_USER", response.data.user)
	commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
	commit("SET_IS_LOGGED", true)

	// todo move someone to routes - injects here are forbidden
	const { producerTeams } = useUserProducer()
	const { hasPermission } = useUserPermission()

	echo.private("relation-requests.user." + state.data.id)
		.listen(".RelationRequestCreated", (e) => {
			console.log(e)
		})

	if (producerTeams.value.length > 0) {
		for (let i in producerTeams.value) {
			if (
				producerTeams.value[i].user_id === state.data.id ||
				hasPermission(producerTeams.value[i].id, ["producer_incoming_coworking_requests"])
			) {
				echo.private("relation-requests.producer." + producerTeams.value[i].id)
					.listen(".RelationRequestCreated", (e) => {
						console.log(e)
					})
			}
		}
	}
}

export const signUp = async ({commit}, payload) => {
	const response = await api.post("register", payload)
	commit("SET_USER", response.data.user)
	commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
	commit("SET_IS_LOGGED", true)
}

export const logout = async ({commit, state}, payload) => {
	echo.disconnect()
	await api.post("personal/logout", payload)
	commit("SET_USER", {})
	commit("userProducer/EMPTY_USER_PRODUCER", {}, { root:true })
	commit("SET_IS_LOGGED", false)
}

export const checkTokenCookie = async ({commit}) => {
	const response = await api.post("hasTokenCookie")
	if (response.data) {
		commit("SET_USER", response.data.user)
		commit("userProducer/SET_USER_PRODUCER", response.data.user_producer, { root:true })
		commit("SET_IS_LOGGED", true)
	}
}

export const verifyEmail = async ({commit}, payload) => {
	await api.post("auth/verify-email", {
		hash: payload.hash,
		email: payload.email
	})
}

export const registerProducer = async ({commit}, payload) => {
	const response = await api.post("personal/register", {...payload, case: 1})
	commit("userProducer/SET_USER_PRODUCER", response.data.producer, { root:true })
	commit("SET_PRODUCER_ROLE", response.data.role)
}

export const switchPersonal = async ({commit}, personal_tab) => {
	commit("SWITCH_PERSONAL", personal_tab)
}

export const sendCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/sendCoworkingRequest", { ...payload })
	commit("SET_USER_OUTGOING_COWORKING_REQUESTS", response.data)
}

export const cancelCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/cancelCoworkingRequest/" + payload.requestId)
	commit("CANCEL_USER_OUTGOING_COWORKING_REQUEST", {
		canceledRequest: response.data,
		canceledStatus: payload.status
	})
}

export const restoreCoworkingRequest = async ({commit}, payload) => {
	const response = await api.post("personal/users/relationRequests/restoreCoworkingRequest/" + payload.requestId)
	commit("RESTORE_USER_OUTGOING_COWORKING_REQUEST", {
		restoredRequest: response.data,
		restoredStatus: payload.status
	})
}
