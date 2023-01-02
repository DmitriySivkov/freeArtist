import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useUserPermission } from "src/composables/userPermission"
import { useUser } from "src/composables/user"
import { useStore } from "vuex"

export const usePrivateChannels = () => {
	const $store = useStore()
	const { user_teams, getTeam } = useUserTeam()
	const { hasPermission } = useUserPermission()
	const { user } = useUser()

	const connectRelationRequestUser = () => {
		// todo - set 'created' hook right
		echo.private("relation-requests.user." + user.value.data.id)
			.listen(".RelationRequestCreated", (e) => {

			})
			.listen(".RelationRequestUpdated", (e) => {
				$store.commit("user/SET_USER_OUTGOING_COWORKING_REQUEST_STATUS", {
					request_id: e.model.id,
					status: e.model.status
				})
				if (e.type === "coworking") {
					$store.commit("user/SET_ROLE", e.role)
					$store.commit("team/SET_USER_TEAMS", e.team)
				}
			})
	}

	const connectRelationRequestIncomingProducer = () => {
		if (user_teams.value.length > 0) {
			for (let i in user_teams.value) {
				if (
					user_teams.value[i].user_id === user.value.data.id ||
					hasPermission(user_teams.value[i].id, ["producer_incoming_coworking_requests"])
				) {
					echo.private("relation-requests.incoming.producer." + user_teams.value[i].detailed.id)
						.listen(".RelationRequestCreated", (e) => {
							$store.commit("team/SET_PRODUCER_INCOMING_RELATION_REQUESTS", {
								incoming_coworking_requests: [e.model],
								producer_id: user_teams.value[i].detailed.id
							})
						})
						.listen(".RelationRequestUpdated", (e) => {
							$store.commit("team/SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
								producer_id: user_teams.value[i].detailed.id,
								request_id: e.model.id,
								status: e.model.status
							})
						})
				}
			}
		}
	}

	const connectPermissions = () => {
		echo.private("permissions." + user.value.data.id)
			.listen(".permissions.synced", (e) => {
				$store.commit("user/SYNC_USER_TEAM_PERMISSIONS", {
					team_id: e.team.id,
					permissions: e.permissions
				})
				if (getTeam(e.team.id).hasOwnProperty("users")) {
					$store.commit("team/SYNC_TEAM_USER_PERMISSIONS", {
						team_id: e.team.id,
						user_id: e.user.id,
						permissions: e.permissions
					})
				}
			})
	}

	return {
		connectRelationRequestUser,
		connectRelationRequestIncomingProducer,
		connectPermissions
	}
}
