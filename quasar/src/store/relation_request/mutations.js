export const SET_USER_REQUESTS = (state, request) => {
	if (state.user_requests.length > 0)
		state.user_requests = [...state.user_requests, Array.isArray(request) ? [...request] : request]
	else
		state.user_requests = Array.isArray(request) ? request : [request]
}

export const SET_USER_TEAMS_REQUESTS = (state, request) => {
	if (state.user_teams_requests.length > 0)
		state.user_teams_requests = [...state.user_teams_requests, Array.isArray(request) ? [...request] : request]
	else
		state.user_teams_requests = Array.isArray(request) ? request : [request]
}

export const EMPTY_USER_REQUESTS = (state) => {
	state.user_requests = []
	state.user_teams_requests = []
}

export const SET_USER_RELATION_REQUEST_STATUS = (state, { request_id, status_id }) => {
	state.user_requests.find((r) => r.id === request_id).status =
		Object.values(state.statuses).find((s) => s.id === status_id)
}

export const SET_USER_TEAM_RELATION_REQUEST_STATUS = (state, { request_id, status_id }) => {
	state.user_teams_requests.find((r) => r.id === request_id).status =
		Object.values(state.statuses).find((s) => s.id === status_id)
}
