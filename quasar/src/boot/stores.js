import { useUserStore } from "src/stores/user"
import { useCartStore } from "src/stores/cart"
import { useProducerOrdersStore } from "src/stores/producerOrders"

export default async ({ store }) => {
	const _userStore = useUserStore(store)
	const _cartStore = useCartStore(store)
	const _producerOrderStore = useProducerOrdersStore(store)
}
