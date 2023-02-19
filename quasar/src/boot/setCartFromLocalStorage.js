import { LocalStorage } from "quasar"
import { useCartStore } from "src/stores/cart"

export default ({ store }) => {
	const cart_store = useCartStore(store)

	if (LocalStorage.has("cart"))
		cart_store.setCart(LocalStorage.getItem("cart"))
}
