import { LocalStorage } from "quasar"
import { useCartStore } from "src/stores/cart"

export default ({ store }) => {
	const cartStore = useCartStore(store)

	if (LocalStorage.has("cart"))
		cartStore.setCart(LocalStorage.getItem("cart"))
}
