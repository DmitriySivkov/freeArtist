import { computed } from "vue"
import { useStore } from "vuex"

export const useUserRole = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const hasUserRole = (roleName) =>
		user.value.data.roles
			.reduce((accum, role) => [...accum, role.name], [])
			.includes(roleName)

	const userRoles = () => user.value.data.roles

	return { user, hasUserRole, userRoles }
}
