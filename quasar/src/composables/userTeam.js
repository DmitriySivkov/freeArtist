import { computed } from "vue"
import { useStore } from "vuex"

export const useUserTeam = () => {
	const $store = useStore()

	const user = computed(() => $store.state.user)

	const team_store = computed(() => $store.state.team)

	const user_teams = computed(() => team_store.value.user_teams)

	const user_own_team = computed(() =>
		user_teams.value.find((team) => team.user_id === user.value.data.id)
	)

	const getTeam = (team_id) =>
		user_teams.value.find((team) => team.id === team_id)

	const getTeamUser = (team_id, user_id) =>
		user_teams.value.find((team) => team.id === team_id)
			.users
			.find((user) => user.id === user_id)

	const syncTeamUserPermissions = (team_id, user_id, permissions) =>
		$store.dispatch(
			"team/syncTeamUserPermissions",
			{ team_id, user_id, permissions }
		)

	const user_teams_producer = computed(() =>
		user_teams.value.filter((t) => t.detailed_type === "App\\Models\\Producer")
	)

	return {
		user_teams,
		user_own_team,
		getTeam,
		getTeamUser,
		syncTeamUserPermissions,
		user_teams_producer
	}
}
