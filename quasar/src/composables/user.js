import { useUserStore } from "src/stores/user"
import { api } from "src/boot/axios"

export const useUser = () => {
	const userStore = useUserStore()

	const afterLogin = (response) => {
		if (response.data.token) {
			api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token
		}

		userStore.setData(response.data.user)
		userStore.setIsLogged(true)
		userStore.setTeams(response.data.user_teams)
	}

	const afterLogout = () => {
		userStore.switchPersonal("user")
		userStore.setData({})
		userStore.emptyTeams()
		userStore.setIsLogged(false)
	}

	const hasPermission = (teamId, permissionName) => {
		return userStore.teams.find((t) => t.id === teamId)
			?.permissions
			.find((p) => p.name === permissionName)
	}

	const hasRole = (roleId) => {
		return userStore.teams.some((t) => t.role_id === roleId)
	}

	const syncTeamUserPermissions = (teamId, userId, permissions) => {
		userStore.syncTeamUserPermissions({ teamId, userId, permissions })
	}

	return {
		afterLogin,
		afterLogout,
		hasPermission,
		hasRole,
		syncTeamUserPermissions,
	}
}
