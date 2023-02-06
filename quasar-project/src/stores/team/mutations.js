import {
	SET_PRODUCER_PRODUCTS,
	SYNC_PRODUCER_PRODUCT_COMMON_SETTINGS,
	SYNC_PRODUCER_PRODUCT_COMPOSITION_SETTINGS,
	CREATE_PRODUCER_PRODUCT,
	DELETE_PRODUCER_PRODUCT,
	ADD_PRODUCER_PRODUCT_IMAGE,
	SET_PRODUCER_LOGO
} from "./extra/producerMutations"

export {
	SET_PRODUCER_PRODUCTS,
	SYNC_PRODUCER_PRODUCT_COMMON_SETTINGS,
	SYNC_PRODUCER_PRODUCT_COMPOSITION_SETTINGS,
	CREATE_PRODUCER_PRODUCT,
	DELETE_PRODUCER_PRODUCT,
	ADD_PRODUCER_PRODUCT_IMAGE,
	SET_PRODUCER_LOGO
}

export const SET_TEAM_USERS = (state, {team_id, users}) => {
	state.user_teams.find((team) => team.id === team_id).users = users
}

export const SET_USER_TEAMS = (state, team) => {
	if (state.user_teams.length > 0)
		state.user_teams = [...state.user_teams, team]
	else
		state.user_teams = Array.isArray(team) ? team : [team]
}

export const EMPTY_USER_TEAMS = (state) => {
	state.user_teams = []
}

export const SYNC_TEAM_USER_PERMISSIONS = (state, { team_id, user_id, permissions }) => {
	const team = state.user_teams.find((team) => team.id === team_id)

	if (team.hasOwnProperty("users"))
		team.users.find((user) => user.id === user_id).permissions = permissions
}

export const SET_TEAM_REQUEST_STATUS = (state, { team_id, request_id, status }) => {
	const requests = state.user_teams.find((t) => t.id === team_id)
		.requests

	requests.data
		.incoming_coworking_requests
		.find((request) => request.id === request_id)
		.status = status

	requests.total_pending_request_count--
}
