import { useUserStore } from "src/stores/user"
import { useTeamStore } from "stores/team"
import { usePermissionStore } from "stores/permission"
import { useRoleStore } from "stores/role"
import { api } from "src/boot/axios"

export const useUser = () => {
	const userStore = useUserStore()
	const teamStore = useTeamStore()
	const permissionStore = usePermissionStore()
	const roleStore = useRoleStore()

	const afterLogin = (response) => {
		if (response.data.token) {
			api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token
		}

		userStore.setData(response.data.user)
		userStore.setIsLogged(true)

		roleStore.setUserRoles(response.data.user_roles)
		permissionStore.setUserPermissions(response.data.user_permissions)

		teamStore.setUserTeams(response.data.user_teams)
	}

	const afterLogout = () => {
		userStore.switchPersonal("user")

		userStore.setData({})
		teamStore.emptyUserTeams()

		userStore.setIsLogged(false)
	}

	return {
		afterLogin,
		afterLogout
	}
}
