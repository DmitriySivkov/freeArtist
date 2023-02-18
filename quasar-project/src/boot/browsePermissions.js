import { usePermissionStore } from "src/stores/permission"

export default async ({ store }) => {
	const permission_store = usePermissionStore(store)

	await permission_store.getList()
}
