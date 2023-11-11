<template>
	<TeamRequestList :team="team" />
</template>

<script setup>
import TeamRequestList from "src/components/teams/TeamRequestList.vue"
import { useRouter } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed } from "vue"

const $router = useRouter()
const { user_teams } = useUserTeam()
// todo - remove team load on init?
const team = computed(() =>
	user_teams.value.find((t) => t.id === parseInt($router.currentRoute.value.params.team_id))
)

</script>
