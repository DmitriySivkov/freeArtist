import { usePermissionStore } from "src/stores/permission"

export const useUserPermission = () => {
	const permission_store = usePermissionStore()

	const hasPermission = (team_id, permission_name) => {
		return permission_store.user_permissions.filter((p) => p.team_id === team_id)
			.map((p) => p.name)
			.includes(permission_name)
	}

	return { hasPermission }
}
