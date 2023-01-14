import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useUser } from "src/composables/user"
import { useStore } from "vuex"
import { useRelationRequestManager } from "src/composables/relationRequestManager"

// todo - make less WS connections ?
export const usePrivateChannels = () => {
	const $store = useStore()
	const { user_teams } = useUserTeam()
	const { user } = useUser()
	const { relation_request } = useRelationRequestManager()

	const connectRelationRequestUser = () => {
		echo.private("relation-requests.user." + user.value.data.id)
			.listen(".RelationRequestUpdated", (e) => {
				$store.commit("relation_request/SET_USER_RELATION_REQUEST_STATUS", {
					request_id: e.model.id,
					status_id: e.model.status.id
				})
				if (e.model.status.id === relation_request.value.statuses.accepted.id) {
					$store.commit("user/SET_ROLE", e.role)
					$store.commit("team/SET_USER_TEAMS", e.team)
					connectPermissions()
				}
			})
	}

	const connectRelationRequestTeam = () => {
		if (user_teams.value.length > 0) {
			for (let i in user_teams.value) {
				echo.private("relation-requests.team." + user_teams.value[i].id)
					.listen(".RelationRequestCreated", (e) => {
						$store.commit("relation_request/SET_USER_TEAMS_REQUESTS", e.model)
					})
					.listen(".RelationRequestUpdated", (e) => {
						$store.commit("relation_request/SET_USER_TEAM_RELATION_REQUEST_STATUS", {
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
						$store.commit("team/SYNC_TEAM_USER_PERMISSIONS", {
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
