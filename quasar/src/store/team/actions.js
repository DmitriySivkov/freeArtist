import { api } from "boot/axios"

export const getUserList = async ({commit}, team_id) => {
	const response = await api.get("personal/teams/" + team_id + "/users")
	commit("SET_TEAM_USERS", {
		team_id,
		users: response.data
	})
}

export const syncTeamUserPermissions = async ({commit}, { team_id, user_id, permissions }) => {
	const response = await api.post(
		"personal/teams/" + team_id + "/permissions/" + user_id + "/sync",
		permissions
	)
	commit("SYNC_TEAM_USER_PERMISSIONS", {
		team_id,
		user_id,
		permissions:response.data
	})
}

export const acceptRequest = async ({commit}, { team_id, request_id }) => {
	const response = await api.post("personal/teams/" + team_id + "/relationRequests/" + request_id + "/accept")
	commit("team/SET_TEAM_REQUEST_STATUS", {
		team_id,
		request_id: response.data.id,
		status: response.data.status
	})
}

export const rejectRequest = async ({commit}, { team_id, request_id }) => {
	const response = await api.post("personal/teams/" + team_id + "/relationRequests/" + request_id + "/reject")
	commit("team/SET_TEAM_REQUEST_STATUS", {
		team_id,
		request_id: response.data.id,
		status: response.data.status
	})
}
