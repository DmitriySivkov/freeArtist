<template>
	<TeamList
		detail-route-name="personal_team_requests_detail"
		:teams="teams"
		:counter="teamPendingRequestCount"
		:use-counter="true"
	/>
</template>

<script>
import TeamList from "src/components/teams/TeamList.vue"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
import { useRelationRequest } from "src/composables/relationRequest"
export default {
	components: { TeamList },
	setup() {
		const { teamPendingRequestCount } = useRelationRequest()
		const { user_teams } = useUserTeam()
		const teams = computed(() => _.orderBy(
			user_teams.value,
			team => team.display_name,
			"asc"
		))

		return {
			teams,
			teamPendingRequestCount
		}
	}
}
</script>
