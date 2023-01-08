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
