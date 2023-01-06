export const SET_USER = (state, data) => {
	state.data = data
}

export const SET_IS_LOGGED = (state, isLogged) => {
	state.isLogged = isLogged
}

export const SWITCH_PERSONAL = (state, personal_tab) => {
	state.personalTab = personal_tab
}

export const SET_ROLE = (state, role) => {
	if (state.data.roles.find((r) => r.id === role.id))
		return

	state.data.roles = [...state.data.roles, role]
}

export const SET_USER_OUTGOING_COWORKING_REQUESTS = (state, request) => {
	if (state.data.hasOwnProperty("outgoing_coworking_requests"))
		state.data.outgoing_coworking_requests = [...state.data.outgoing_coworking_requests, request]
	else
		state.data.outgoing_coworking_requests = [request]
}

export const SET_USER_OUTGOING_COWORKING_REQUEST_STATUS = (state, {request_id, status}) => {
	const request = state.data.outgoing_coworking_requests.find((request) => request.id === request_id)
	request.status = status
}

export const CANCEL_USER_REQUEST = (state, payload) => {
	state.data.outgoing_coworking_requests
		.find((request) => request.id === payload.canceledRequest.id)
		.status = payload.canceledStatus
}

export const RESTORE_USER_REQUEST = (state, payload) => {
	state.data.outgoing_coworking_requests
		.find((request) => request.id === payload.restoredRequest.id)
		.status = payload.restoredStatus
}

export const SYNC_USER_TEAM_PERMISSIONS = (state, {team_id, permissions}) => {
	state.data.permissions = state.data.permissions.filter((p) => p.pivot.team_id !== team_id)
	if (permissions.length !== 0)
		state.data.permissions.push(...permissions)
}
