import { defineStore } from "pinia"
import { api } from "src/boot/axios"
import { useRelationRequestStore } from "src/stores/relation-request"

export const useTeamStore = defineStore("team", {
	state: () => ({
		user_teams: []
	}),

	actions: {
		updateTeamFields({team_id, fields}) {
			this.setTeamFields({
				team_id,
				fields
			})

			return api.put(
				"personal/teams/" + team_id,
				fields
			)
		},

		// todo - rename all NOT async 'set' methods to 'commit'
		setTeamFields({team_id, fields, detailed_id}) {
			let team = this.user_teams.find((t) => t.id === team_id)

			// if 'detailed_id' is not set then we update 'teams' entity
			// otherwise we update an entity morphed to teams

			if (!detailed_id) {
				Object.assign(team, fields)
			} else {
				team.detailed = { ...team.detailed, ...fields }
			}
		},

		setUserTeams(team) {
			if (this.user_teams.length > 0)
				this.user_teams = [...this.user_teams, team]
			else
				this.user_teams = Array.isArray(team) ? team : [team]
		},

		emptyUserTeams() {
			this.user_teams = []
		},

		setTeamUsers({team_id, users}) {
			this.user_teams.find((team) => team.id === team_id).users = users
		},

		async getUserList(team_id) {
			const response = await api.get("personal/teams/" + team_id + "/users")

			this.setTeamUsers({ team_id, users: response.data})
		},

		commitTeamUserPermissions({ team_id, user_id, permissions }) {
			const team = this.user_teams.find((team) => team.id === team_id)

			if (team.hasOwnProperty("users")) {
				const team_user = team.users.find((user) => user.id === user_id)

				if (team_user) {
					team_user.permissions = permissions
				}
			}
		},

		async syncTeamUserPermissions({ team_id, user_id, permissions }) {
			const response = await api.post(
				"personal/teams/" + team_id + "/users/" + user_id + "/permissions/sync",
				permissions
			)

			this.commitTeamUserPermissions({
				team_id,
				user_id,
				permissions:response.data
			})
		},

		async acceptRequest({ team_id, request_id }) {
			const response = await api.post("personal/teams/" + team_id + "/relationRequests/" + request_id + "/accept")

			const relation_request_store = useRelationRequestStore()

			relation_request_store.setUserTeamRelationRequestStatus({
				request_id: response.data.id,
				status_id: response.data.status.id
			})
		},

		async rejectRequest({ team_id, request_id }) {
			const response = await api.post("personal/teams/" + team_id + "/relationRequests/" + request_id + "/reject")

			const relation_request_store = useRelationRequestStore()

			relation_request_store.setUserTeamRelationRequestStatus({
				request_id: response.data.id,
				status_id: response.data.status.id
			})
		}

	}
})
