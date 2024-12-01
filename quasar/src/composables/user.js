import { useUserStore } from "src/stores/user"
import { Platform } from "quasar"
import { api } from "src/boot/axios"
import { useStorage } from "src/composables/storage"

export const useUser = () => {
	const userStore = useUserStore()

	const storage = useStorage()

	const afterLogin = async(response) => {
		if (response.data.token) {
			api.defaults.headers.common["Authorization"] = "Bearer " + response.data.token

			if (Platform.is.capacitor) {
				await storage.set({
					key: "token",
					value: response.data.token
				})
			}
		}

		userStore.setData(response.data.user)
		userStore.setTeams(response.data.user_teams)

		if (response.data.auth_transaction_uuids?.length) {
			if (await storage.has("orders")) {
				const storageOrderUuids = await storage.get("orders")

				// todo - if from mobile app theres a chance that no space available on device.
				await storage.set({
					key: "orders",
					value: [...storageOrderUuids, ...response.data.auth_transaction_uuids],
				})
			} else {
				await storage.set({
					key: "orders",
					value: response.data.auth_transaction_uuids,
				})
			}
		}

		userStore.setIsLogged(true)
	}

	const afterLogout = () => {
		userStore.switchPersonal("user")
		userStore.setData({})
		userStore.emptyTeams()
		userStore.setIsLogged(false)
	}

	const hasPermission = (teamId, permissionName) => {
		return !!userStore.teams.find((t) => t.id === teamId)
			?.permissions
			?.find((p) => p.name === permissionName)
	}

	const hasRole = (roleId) => {
		return userStore.teams.some((t) => t.role_id === roleId)
	}

	return {
		afterLogin,
		afterLogout,
		hasPermission,
		hasRole,
	}
}
