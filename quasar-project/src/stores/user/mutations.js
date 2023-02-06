import { LocalStorage } from "quasar"

export const SET_USER = (state, data) => {
	state.data = data
}

export const SET_IS_LOGGED = (state, is_logged) => {
	state.is_logged = is_logged
}

export const SWITCH_PERSONAL = (state, personal_tab) => {
	state.personal_tab = personal_tab

	LocalStorage.set("personal_tab", personal_tab)
}

export const SET_ROLE = (state, role) => {
	if (state.data.roles.find((r) => r.id === role.id))
		return

	state.data.roles = [...state.data.roles, role]
}

export const SET_LOCATION = (state, location) => {
	state.location = location
}

export const SET_LOCATION_RANGE = (state, range) => {
	state.location_range = range
}
