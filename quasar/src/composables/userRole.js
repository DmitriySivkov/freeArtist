import { computed } from "vue"
import { useRoleStore } from "src/stores/role"

export const useUserRole = () => {
	const role_store = useRoleStore()

	const hasUserRole = (roleName) =>
		role_store.user_roles
			.reduce((accum, role) => [...accum, role.name], [])
			.includes(roleName)

	const user_roles = computed(() => role_store.user_roles)

	return { hasUserRole, user_roles }
}
