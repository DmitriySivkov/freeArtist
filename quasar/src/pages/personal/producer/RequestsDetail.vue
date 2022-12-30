<template>
	<ProducerRequestList :producer="team"/>
</template>

<script>
import ProducerRequestList from "src/components/producers/ProducerRequestList"
import { useRoute } from "vue-router"
import { useUserTeam } from "src/composables/userTeam"
import { computed } from "vue"

export default {
	components: { ProducerRequestList },
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
