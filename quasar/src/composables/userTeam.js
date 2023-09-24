import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { useTeamStore } from "src/stores/team"

export const useUserTeam = () => {
	const user_store = useUserStore()
	const team_store = useTeamStore()

	const user_teams = computed(() => team_store.user_teams)

	const userOwnTeam = computed(() =>
		user_teams.value.find((team) => team.user_id === user_store.data.id)
	)

	const getTeam = (team_id) =>
		user_teams.value.find((team) => team.id === team_id)

	const getTeamUser = (team_id, user_id) =>
		user_teams.value.find((team) => team.id === team_id)
			.users
			.find((user) => user.id === user_id)

	const syncTeamUserPermissions = (team_id, user_id, permissions) =>
		team_store.syncTeamUserPermissions({ team_id, user_id, permissions })

	const user_teams_producer = computed(() =>
		user_teams.value.filter((t) => t.detailed_type === "App\\Models\\Producer")
	)

	return {
		user_teams,
		userOwnTeam,
		getTeam,
		getTeamUser,
		syncTeamUserPermissions,
		user_teams_producer
	}
}
