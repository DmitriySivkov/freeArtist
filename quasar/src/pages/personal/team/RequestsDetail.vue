<template>
	<TeamRequestList :team="team" />
</template>

<script>
import TeamRequestList from "src/components/teams/TeamRequestList"
import { useRoute } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed } from "vue"
export default {
	components: { TeamRequestList },
	setup() {
		const $route = useRoute()
		const { user_teams } = useUserTeam()
		const team = computed(() =>
			user_teams.value.find((t) => t.id === parseInt($route.params.team_id))
		)

		return {
			team
		}
	}
}
</script>
