import { computed } from "vue"
import { useUserStore } from "src/stores/user"

export const useUserRole = () => {
	const user_store = useUserStore()
	const user = computed(() => user_store.$state)

	const hasUserRole = (roleName) =>
		user.value.data.roles
			.reduce((accum, role) => [...accum, role.name], [])
			.includes(roleName)

	const userRoles = computed(() => user.value.data.roles)

	return { user, hasUserRole, userRoles }
}
