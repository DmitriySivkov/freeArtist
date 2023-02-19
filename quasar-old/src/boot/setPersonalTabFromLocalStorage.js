import { LocalStorage } from "quasar"
export default ({ store }) => {
	if (LocalStorage.has("personal_tab") && store.state.user.is_logged) {
		let personal_tab = LocalStorage.getItem("personal_tab")
		let user_roles = store.state.user.data.roles.reduce((accum, role) => [...accum, role.name], [])

		if (user_roles.includes(personal_tab))
			store.commit("user/SWITCH_PERSONAL", personal_tab)
	}
}
