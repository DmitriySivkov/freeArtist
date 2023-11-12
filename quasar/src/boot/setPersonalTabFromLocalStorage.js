import { LocalStorage } from "quasar"
import { useUserStore } from "src/stores/user"
import { useRoleStore } from "src/stores/role"

export default ({ store }) => {
	const userStore = useUserStore(store)
	const roleStore = useRoleStore(store)

	if (LocalStorage.has("personal_tab") && userStore.is_logged) {
		let personalTab = LocalStorage.getItem("personal_tab")
		let userRoleNames = roleStore.user_roles.map((r) => r.name)

		if (userRoleNames.includes(personalTab)) {
			userStore.switchPersonal(personalTab)
		}
	}
}
