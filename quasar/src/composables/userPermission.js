import { computed } from "vue"
import { useStore } from "vuex"

export const useUserPermission = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const team_store = computed(() => $store.state.team)

	const hasPermission = (team_id, permission_name) => {
		const team = team_store.value.user_teams.find((t) => t.id === team_id)

		const team_user = team.hasOwnProperty("users") ?
			team.users.find((u) => u.id === user.value.data.id) : false

		return user.value.data.permissions.filter((p) => p.pivot.team_id === team_id)
			.map((p) => p.name)
			.includes(permission_name) ||
		team.user_id === user.value.data.id ||
		(team_user ? team_user.permissions.map((p) => p.name).includes(permission_name) : false)
	}

	return { hasPermission }
}
