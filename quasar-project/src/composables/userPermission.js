import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"

export const useUserPermission = () => {
	const user_store = useUserStore()
	const team_store = useTeamStore()

	const hasPermission = (team_id, permission_name) => {
		const team = team_store.user_teams.find((t) => t.id === team_id)

		const team_user = team.hasOwnProperty("users") ?
			team.users.find((u) => u.id === user_store.data.id) : false

		return user_store.data.permissions.filter((p) => p.pivot.team_id === team_id) // if user already has permission loaded on auth
			.map((p) => p.name)
			.includes(permission_name) ||
		team.user_id === user_store.data.id || // if user is team owner
		(team_user ? team_user.permissions.map((p) => p.name).includes(permission_name) : false) // if user just got permissions in real time
	}

	return { hasPermission }
}
