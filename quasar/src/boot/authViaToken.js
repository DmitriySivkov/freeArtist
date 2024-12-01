import { Platform } from "quasar"
import { SecureStoragePlugin } from "capacitor-secure-storage-plugin"
import { api } from "src/boot/axios"
import { useUser } from "src/composables/user"
import { useUserStore } from "src/stores/user"

export default async () => {
	const userStore = useUserStore()

	const { afterLogin } = useUser()

	let token = null

	if (Platform.is.capacitor) {
		try {
			token = await SecureStoragePlugin.get({key: "token"})
		} catch (error) {
			// if token is not found - throws error
		}
	}

	if (token && token.value) {
		api.defaults.headers.common["Authorization"] = "Bearer " + token.value
	}

	const response = await api.post("auth/viaToken")

	if (response.data) {
		await afterLogin(response)

		userStore.setIsLogged(true)
	}
}
