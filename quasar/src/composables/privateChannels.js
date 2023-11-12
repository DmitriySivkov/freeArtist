import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useTeamStore } from "src/stores/team"
import { useUserStore } from "src/stores/user"
import { usePermissionStore } from "src/stores/permission"
import { useRoleStore } from "src/stores/role"
import { ROLES } from "src/const/roles"

export const usePrivateChannels = () => {
	// todo rm computed from composable
	const { user_teams } = useUserTeam()

	const userStore = useUserStore()
	const teamStore = useTeamStore()
	const permissionStore = usePermissionStore()
	const roleStore = useRoleStore()

	const connectTeams = () => {
		// todo - same for 'teams' entity - add name change to producer profile
		if (user_teams.value.length === 0)
			return

		for (let i in user_teams.value) {
			echo.private("teams." + user_teams.value[i].id)
				.listen(".teams.updated", (e) => {
					teamStore.setTeamFields({
						team_id: e.teamId,
						fields: e.fields,
						detailed_id: e.detailedId
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
					teamStore.commitTeamUserPermissions({
						team_id: e.team.id,
						user_id: e.user.id,
						permissions: e.permissions
					})

					if (e.user.id === userStore.data.id) {
						permissionStore.syncUserTeamPermissions({
							team_id: e.team.id,
							permissions: e.permissions
						})
					}

				})
		}
	}

	const connectRelationRequests = () => {
		echo.private(`relation-requests.user.${userStore.data.id}`)
			.listen(".user-team.relation-request.accepted", (e) => {
				roleStore.setUserRole({
					id: ROLES.PRODUCER,
					team_id: e.team.id
				})

				teamStore.setUserTeams(e.team)
			})
	}

	return {
		connectTeams,
		connectPermissions,
		connectRelationRequests
	}
}
