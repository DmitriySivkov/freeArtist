<template>
	<TeamList
		detail-route-name="personal_producer_profile_detail"
		:teams="teams"
	/>
</template>

<script>
import TeamList from "src/components/teams/TeamList.vue"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
export default {
	components: { TeamList },
	setup() {
		const { user_teams } = useUserTeam()
		const teams = computed(() => _.orderBy(
			user_teams.value,
			team => team.display_name,
			"asc"
		))

		return {
			teams
		}
	}
}
</script>
