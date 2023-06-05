import { useUserStore } from "src/stores/user"
import { usePermissionStore } from "src/stores/permission"
import { useProducerStore } from "src/stores/producer"
import { useOrderStore } from "src/stores/order"
import { useCartStore } from "src/stores/cart"
import { useRelationRequestStore } from "src/stores/relation-request"
import { useTeamStore } from "src/stores/team"

export default async ({ store }) => {
	const _userStore = useUserStore(store)
	const _permissionStore = usePermissionStore(store)
	const _producerStore = useProducerStore(store)
	const _orderStore = useOrderStore(store)
	const _cartStore = useCartStore(store)
	const _relationRequestStore = useRelationRequestStore(store)
	const _teamStore = useTeamStore(store)
}
