import { computed } from "vue"
import { useStore } from "vuex"

export const useUserRole = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const userRoles = computed(() =>
		user.value.data.roles.reduce((accum, role) => [...accum, role.id], [])
	)

	const hasUserRole = (roleId) => {
		return userRoles.value.includes(roleId)
	}

	return { user, hasUserRole }
}
