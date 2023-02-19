import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useUser } from "src/composables/user"
import { useRelationRequestManager } from "src/composables/relationRequestManager"
import { useTeamStore } from "src/stores/team"
import { useUserStore } from "src/stores/user"
import { useRelationRequestStore } from "src/stores/relation-request"

// todo - make less WS connections ?
export const usePrivateChannels = () => {
	const { user_teams } = useUserTeam()
	const { user } = useUser()
	const { relation_request } = useRelationRequestManager()

	const user_store = useUserStore()
	const team_store = useTeamStore()
	const relation_request_store = useRelationRequestStore()

	const connectRelationRequestUser = () => {
		echo.private("relation-requests.user." + user.value.data.id)
			.listen(".RelationRequestUpdated", (e) => {
				relation_request_store.setUserRelationRequestStatus({
					request_id: e.model.id,
					status_id: e.model.status.id
				})

				if (e.model.status.id === relation_request.value.statuses.accepted.id) {
					user_store.setRole(e.role)
					team_store.setUserTeams(e.team)
					connectPermissions()
				}
			})
	}

	const connectRelationRequestTeam = () => {
		if (user_teams.value.length > 0) {
			for (let i in user_teams.value) {
				echo.private("relation-requests.team." + user_teams.value[i].id)
					.listen(".RelationRequestCreated", (e) => {
						relation_request_store.setUserTeamsRequests(e.model)
					})
					.listen(".RelationRequestUpdated", (e) => {
						relation_request_store.setUserTeamRelationRequestStatus({
							request_id: e.model.id,
							status_id: e.model.status.id
						})
					})
			}
		}
	}

	const connectPermissions = () => {
		if (user_teams.value.length > 0) {
			for (let i in user_teams.value) {
				echo.private("permissions." + user_teams.value[i].id)
					.listen(".permissions.synced", (e) => {
						team_store.commitTeamUserPermissions({
							team_id: e.team.id,
							user_id: e.user.id,
							permissions: e.permissions
						})
					})
			}
		}
	}

	return {
		connectRelationRequestUser,
		connectRelationRequestTeam,
		connectPermissions
	}
}
