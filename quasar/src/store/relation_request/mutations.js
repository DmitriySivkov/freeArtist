export const SET_USER_REQUESTS = (state, request) => {
	if (state.user_requests.length > 0)
		state.user_requests = [...state.user_requests, request]
	else
		state.user_requests = Array.isArray(request) ? request : [request]
}

export const SET_USER_TEAMS_REQUESTS = (state, request) => {
	if (state.user_teams_requests.length > 0)
		state.user_teams_requests = [...state.user_teams_requests, request]
	else
		state.user_teams_requests = Array.isArray(request) ? request : [request]
}

export const EMPTY_USER_REQUESTS = (state) => {
	state.user_requests = []
	state.user_teams_requests = []
}


