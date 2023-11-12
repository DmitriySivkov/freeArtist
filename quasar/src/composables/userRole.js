import { useRoleStore } from "src/stores/role"

export const useUserRole = () => {
	const roleStore = useRoleStore()

	const hasUserRole = (roleName) =>
		roleStore.user_roles
			.reduce((accum, role) => [...accum, role.name], [])
			.includes(roleName)

	return { hasUserRole }
}
