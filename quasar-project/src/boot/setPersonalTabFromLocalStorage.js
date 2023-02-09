import { LocalStorage } from "quasar"
import { useUserStore } from "src/stores/user"

export default ({ store }) => {
  const user_store = useUserStore(store)
	if (LocalStorage.has("personal_tab") && user_store.is_logged) {
		let personal_tab = LocalStorage.getItem("personal_tab")
		let user_roles = user_store.data.roles.reduce((accum, role) => [...accum, role.name], [])

		if (user_roles.includes(personal_tab))
			user_store.switchPersonal(personal_tab)
	}
}
