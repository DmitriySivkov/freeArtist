export const SET_USER = (state, data) => {
	state.data = data
}

export const SET_IS_LOGGED = (state, isLogged) => {
	state.isLogged = isLogged
}

export const SET_USER_PRODUCER = (state, producer) => {
	state.data.producers = [...state.data.producers, producer]
}
