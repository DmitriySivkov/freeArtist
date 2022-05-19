import { LocalStorage } from "quasar"

export default ({ store }) => {
	if (LocalStorage.has("cart"))
		store.dispatch("cart/setCart", LocalStorage.getItem("cart"))
}
