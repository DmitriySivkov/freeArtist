export const SET_USER = (state, data) => {
	state.data = data
}

export const SET_IS_LOGGED = (state, isLogged) => {
	state.isLogged = isLogged
}

export const SWITCH_PERSONAL = (state, personal_tab) => {
	state.personalTab = personal_tab
}

export const SET_PRODUCER_ROLE = (state, producerRole) => {
	if (state.data.roles.find((role) => role.id === producerRole.id))
		return

	state.data.roles = [...state.data.roles, producerRole]
}

export const SET_USER_OUTGOING_COWORKING_REQUESTS = (state, request) => {
	if (state.data.hasOwnProperty("outgoing_coworking_requests"))
		state.data.outgoing_coworking_requests = [...state.data.outgoing_coworking_requests, request]
	else
		state.data.outgoing_coworking_requests = [request]
}

export const CANCEL_USER_OUTGOING_COWORKING_REQUEST = (state, payload) => {
	state.data.outgoing_coworking_requests
		.find((request) => request.id === payload.canceledRequest.id)
		.status = payload.canceledStatus
}

export const RESTORE_USER_OUTGOING_COWORKING_REQUEST = (state, payload) => {
	state.data.outgoing_coworking_requests
		.find((request) => request.id === payload.restoredRequest.id)
		.status = payload.restoredStatus
}
