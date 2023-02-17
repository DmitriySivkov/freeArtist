import { echo } from "boot/ws"
import { useUserTeam } from "src/composables/userTeam"
import { useUser } from "src/composables/user"
import { useStore } from "vuex"
import { useRelationRequestManager } from "src/composables/relationRequestManager"
import { useTeamStore } from "src/stores/team"
import { useRelationRequestStore } from "src/stores/relation-request";

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
    const relation_request_store = useRelationRequestStore()
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
    const team_store = useTeamStore()
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
