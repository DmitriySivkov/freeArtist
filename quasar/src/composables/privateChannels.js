import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useTeamStore } from "src/stores/team"
import { useUserStore } from "src/stores/user"
import { useRelationRequestStore } from "src/stores/relation-request"
import { usePermissionStore } from "src/stores/permission"
import { useRoleStore } from "stores/role"

// todo - make less WS connections ?
export const usePrivateChannels = () => {
	const { user_teams } = useUserTeam()

	const user_store = useUserStore()
	const team_store = useTeamStore()
	const relation_request_store = useRelationRequestStore()
	const permission_store = usePermissionStore()
	const role_store = useRoleStore()

	const connectTeams = () => {
		// todo - same for 'teams' entity - add name change to producer profile
		if (user_teams.value.length === 0)
			return

		for (let i in user_teams.value) {
			echo.private("teams." + user_teams.value[i].id)
				.listen(".teams.updated", (e) => {
					team_store.setTeamFields({
						team_id: e.teamId,
						fields: e.fields,
						detailed_id: e.detailedId
					})
				})
		}

	}

	const connectRelationRequestUser = () => {
		echo.private("relation-requests.user." + user_store.data.id)
			.listen(".RelationRequestUpdated", (e) => {
				relation_request_store.setUserRelationRequestStatus({
					request_id: e.model.id,
					status_id: e.model.status.id
				})

				if (e.model.status.id === relation_request_store.statuses.accepted.id) {
					role_store.setUserRole(e.role)
					team_store.setUserTeams(e.team)
					connectPermissions()
				}
			})
	}

	const connectRelationRequestTeam = () => {
		if (user_teams.value.length === 0)
			return

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

	const connectPermissions = () => {
		if (user_teams.value.length === 0)
			return

		for (let i in user_teams.value) {
			echo.private("permissions." + user_teams.value[i].id)
				.listen(".permissions.synced", (e) => {
					team_store.commitTeamUserPermissions({
						team_id: e.team.id,
						user_id: e.user.id,
						permissions: e.permissions
					})

					if (e.user.id === user_store.data.id) {
						permission_store.syncUserTeamPermissions({
							team_id: e.team.id,
							permissions: e.permissions
						})
					}

				})
		}

	}

	return {
		connectTeams,
		connectRelationRequestUser,
		connectRelationRequestTeam,
		connectPermissions
	}
}
