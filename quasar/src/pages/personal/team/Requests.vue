<template>
	<TeamList
		detail-route-name="personal_team_requests_detail"
		:teams="teams"
		:counter="producerPendingRequestCount"
		:use-counter="true"
	/>
</template>

<script>
import TeamList from "src/components/teams/TeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
import { useRelationRequestManager } from "src/composables/relationRequestManager"
export default {
	components: { TeamList },
	setup() {
		const { producerPendingRequestCount } = useRelationRequestManager()
		const { user_teams } = useUserTeam()
		const teams = computed(() => _.orderBy(
			user_teams.value,
			team => team.display_name,
			"asc"
		))

		return {
			teams,
			producerPendingRequestCount
		}
	}
}
</script>
