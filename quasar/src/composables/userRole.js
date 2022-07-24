import { computed } from "vue"
import { useStore } from "vuex"

export const useUserRole = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const roles = computed(() => $store.state.role)

	const hasUserRole = (roleName) =>
		user.value.data.roles
			.reduce((accum, role) => [...accum, role.name], [])
			.includes(roleName)

	const userRoles = () => user.value.data.roles

	const allRoles = () => roles.value.data

	return { user, hasUserRole, userRoles }
}
