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

export const SET_USER_PRODUCER_JOIN_REQUESTS = (state, join_request) => {
	if (state.data.hasOwnProperty("producer_join_requests"))
		state.data.producer_join_requests = [...state.data.producer_join_requests, join_request]
	else
		state.data.producer_join_requests = [join_request]
}
