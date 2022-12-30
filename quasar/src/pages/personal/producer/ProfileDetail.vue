<template>
	<ProducerProfile :producer="team"/>
</template>

<script>
import { useRoute } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed } from "vue"
import ProducerProfile from "src/components/producers/ProducerProfile"
export default {
	components: { ProducerProfile },
	setup() {
		const $route = useRoute()
		const { user_teams } = useUserTeam()
		const team = computed(() =>
			user_teams.value.find((team) => team.id === parseInt($route.params.team_id))
		)

		return {
			team
		}
	}
}
</script>
