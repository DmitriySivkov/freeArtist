import { LocalStorage } from "quasar"

export default ({ store }) => {
	if (LocalStorage.has("personal_tab"))
		store.commit("user/SWITCH_PERSONAL", LocalStorage.getItem("personal_tab"))
}
