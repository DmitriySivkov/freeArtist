<template>
	<ProducerRequestList :team="team"/>
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
			user_teams.value.find((t) => t.detailed.id === parseInt($route.params.producer_id))
		)

		return {
			team
		}
	}
}
</script>
