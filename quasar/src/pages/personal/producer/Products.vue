<template>
	<TeamList
		detail-route-name="personal_producer_products_detail"
		:teams="producerTeamsSorted"
	/>
</template>

<script>
import TeamList from "src/components/teams/TeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
export default {
	components: { TeamList },
	setup() {
		const { user_teams } = useUserTeam()
		const producerTeamsSorted = computed(() => _.orderBy(
			user_teams.value,
			team => team.display_name,
			"asc"
		))

		return {
			producerTeamsSorted
		}
	}
}
</script>
