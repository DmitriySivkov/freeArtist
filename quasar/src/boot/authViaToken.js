import { Loading, Platform } from "quasar"
import { Plugins } from "@capacitor/core"
import { useUserStore } from "src/stores/user"

export default async ({ store }) => {
	const user_store = useUserStore(store)

	Loading.show({
		spinnerColor: "primary",
	})
	const { Storage } = Plugins

	let token = null

	if (Platform.is.capacitor)
		token = await Storage.get({ key: "token" })

	await user_store.authViaToken({ token })
}
