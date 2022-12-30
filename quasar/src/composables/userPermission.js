import { computed } from "vue"
import { useStore } from "vuex"

export const useUserPermission = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const team_store = computed(() => $store.state.team)

	const hasPermission = (team_id, permission_name) =>
		user.value.data
			.permissions
			.filter((p) => p.pivot.team_id === team_id)
			.map((p) => p.name)
			.includes(permission_name) ||
		team_store.value.user_teams
			.find((t) => t.id === team_id)
			.user_id === user.value.data.id

	return { hasPermission }
}
