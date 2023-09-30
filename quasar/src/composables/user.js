import { useUserStore } from "src/stores/user"
import { useTeamStore } from "stores/team"
import { useRelationRequestStore } from "stores/relation-request"
import { usePermissionStore } from "stores/permission"
import { useRoleStore } from "stores/role"
import { api } from "src/boot/axios"

export const useUser = () => {
	const userStore = useUserStore()
	const teamStore = useTeamStore()
	const relationRequestStore = useRelationRequestStore()
	const permissionStore = usePermissionStore()
	const roleStore = useRoleStore()

	// todo remove heavy loadings (relation requests & etc)
	const afterLogin = (response) => {
		if (response.data.token)
			api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

		userStore.setData(response.data.user)

		roleStore.setUserRoles(response.data.user_roles)
		permissionStore.setUserPermissions(response.data.user_permissions)

		teamStore.setUserTeams(response.data.user_teams)

		if (response.data.user_requests.length > 0)
			relationRequestStore.commitUserRequest(response.data.user_requests)

		if (response.data.user_teams_requests.length > 0)
			relationRequestStore.setUserTeamsRequests(response.data.user_teams_requests)
	}

	return {
		afterLogin
	}
}
