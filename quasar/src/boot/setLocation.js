import { useUserStore } from "src/stores/user"
import { LocalStorage } from "quasar"

export default async ({ store }) => {
	const user_store = useUserStore(store)

	if (LocalStorage.has("location"))
		user_store.setLocation(LocalStorage.getItem("location"))
}
