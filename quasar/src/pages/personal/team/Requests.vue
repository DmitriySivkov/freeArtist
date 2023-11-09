<template>
	<TeamList
		detail-route-name="personal_team_requests_detail"
		:teams="teams"
	/>
</template>

<script setup>
import TeamList from "src/components/teams/TeamList.vue"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"

const { user_teams } = useUserTeam()
const teams = computed(() => _.orderBy(
	user_teams.value,
	team => team.display_name,
	"asc"
))
</script>
