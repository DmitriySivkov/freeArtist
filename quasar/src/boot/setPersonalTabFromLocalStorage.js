import { LocalStorage } from "quasar"
import { useUserStore } from "src/stores/user"
import { useUserRole } from "src/composables/userRole"

export default ({ store }) => {
	const user_store = useUserStore(store)
	const { user_roles } = useUserRole()

	if (LocalStorage.has("personal_tab") && user_store.is_logged) {
		let personal_tab = LocalStorage.getItem("personal_tab")
		let user_role_names = user_roles.value.reduce((accum, role) => [...accum, role.name], [])

		if (user_role_names.includes(personal_tab))
			user_store.switchPersonal(personal_tab)
	}
}
