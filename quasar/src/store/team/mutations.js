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

export const SYNC_TEAM_USER_PERMISSIONS = (state, {team_id, user_id, permissions}) => {
	state.user_teams.find((team) => team.id === team_id)
		.users
		.find((user) => user.id === user_id)
		.permissions = permissions
}
