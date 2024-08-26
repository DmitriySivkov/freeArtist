import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useTeamStore } from "src/stores/team"
import { useUserStore } from "src/stores/user"
import { usePermissionStore } from "src/stores/permission"
import { useRoleStore } from "src/stores/role"
import { useProducerOrdersStore } from "src/stores/producerOrders"

import { ROLES } from "src/const/roles"

export const usePrivateChannels = () => {
	const userStore = useUserStore()
	const teamStore = useTeamStore()
	const permissionStore = usePermissionStore()
	const roleStore = useRoleStore()
	const producerOrderStore = useProducerOrdersStore()

	const userTeams = teamStore.user_teams // todo - change 'user_teams' to 'data'

	const connectTeams = () => {
		// todo - same for 'teams' entity - add name change to producer profile
		if (userTeams.length === 0)
			return

		for (let i in userTeams) {
			echo.private("teams." + userTeams[i].id)
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
		if (userTeams.length === 0)
			return

		for (let i in userTeams) {
			echo.private("permissions." + userTeams[i].id)
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

	const connectProducerOrders = () => {
		const userProducers = userTeams.filter((t) => t.detailed_type === "App\\Models\\Producer")

		if (userProducers.length === 0)
			return

		for (let i in userProducers) {
			echo.private(`producers.${userProducers[i].id}.orders`)
				.listen(".order.updated", (e) => {
					producerOrderStore.commitOrderFields({
						orderId: e.model.id,
						fields: e.model,
					})
				})
		}
	}

	return {
		connectTeams,
		connectPermissions,
		connectRelationRequests,
		connectProducerOrders
	}
}
