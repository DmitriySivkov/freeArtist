import { useUserStore } from "src/stores/user"
import { usePermissionStore } from "src/stores/permission"
import { useProducerStore } from "src/stores/producer"
import { useCartStore } from "src/stores/cart"
import { useTeamStore } from "src/stores/team"

export default async ({ store }) => {
	const _userStore = useUserStore(store)
	const _permissionStore = usePermissionStore(store)
	const _producerStore = useProducerStore(store)
	const _cartStore = useCartStore(store)
	const _teamStore = useTeamStore(store)
}
