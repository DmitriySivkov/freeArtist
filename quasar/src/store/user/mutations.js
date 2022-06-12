export const SET_USER = (state, data) => {
	state.data = data
}

export const SET_IS_LOGGED = (state, isLogged) => {
	state.isLogged = isLogged
}

export const SET_USER_PRODUCER = (state, producer) => {
	if (state.data.hasOwnProperty("producers"))
		state.data.producers = [...state.data.producers, producer]
	else
		state.data.producers = [producer]
}

export const SET_USER_OUTGOING_JOIN_REQUESTS = (state, request) => {
	if (state.data.hasOwnProperty("outgoing_join_requests"))
		state.data.outgoing_join_requests = [...state.data.outgoing_join_requests, request]
	else
		state.data.outgoing_join_requests = [request]
}
