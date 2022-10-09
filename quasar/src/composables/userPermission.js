import { computed } from "vue"
import { useStore } from "vuex"

export const useUserPermission = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const hasPermission = (team_id, permission_name) => {
		return user.value.data
			.permissions
			.filter((p) => p.pivot.team_id === team_id)
			.map((p) => p.name)
			.includes(permission_name)
	}

	return { hasPermission }
}
