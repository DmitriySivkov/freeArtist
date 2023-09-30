import { Platform } from "quasar"
import { Plugins } from "@capacitor/core"
import { api } from "src/boot/axios"
import { useUser } from "src/composables/user"

export default async () => {
	const { afterLogin } = useUser()

	const { Storage } = Plugins

	let token = null

	if (Platform.is.capacitor) {
		token = await Storage.get({key: "token"})
	}

	if (token && token.value)
		api.defaults.headers.common["Authorization"] = "Bearer " + token.value

	const response = await api.post("auth/viaToken")

	// todo - remove heavy payload (user requests / user team requests / etc)
	if (response.data) {
		afterLogin(response)

		this.setIsLogged(true)
	}
}
