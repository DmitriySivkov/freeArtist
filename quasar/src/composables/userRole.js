import { computed } from "vue"
import { useStore } from "vuex"

export const useUserRole = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const hasUserRole = (roleId) =>
		user.value.data.roles
			.reduce((accum, role) => [...accum, role.id], [])
			.includes(roleId)

	const getUserRoles = () =>
		user.value.data.roles

	return { user, hasUserRole, getUserRoles }
}
