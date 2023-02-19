import { Loading, Platform } from "quasar"
import { Plugins } from "@capacitor/core"

export default async ({ store }) => {
	Loading.show({
		spinnerColor: "primary",
	})
	const { Storage } = Plugins

	let token = null

	if (Platform.is.capacitor)
		token = await Storage.get({ key: "token" })

	await store.dispatch("user/authViaToken", { token })
}
