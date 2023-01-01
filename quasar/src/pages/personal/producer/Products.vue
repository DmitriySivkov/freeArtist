<template>
	<ProducerTeamList
		detail-route-name="personal_producer_products_detail"
		:teams="producerTeamsSorted"
	/>
</template>

<script>
import ProducerTeamList from "src/components/producers/ProducerTeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
export default {
	components: { ProducerTeamList },
	setup() {
		const { user_teams } = useUserTeam()
		const producerTeamsSorted = computed(() => _.orderBy(
			user_teams.value.filter((t) => t.detailed_type === "App\\Models\\Producer"),
			team => team.display_name,
			"asc"
		))

		return {
			producerTeamsSorted
		}
	}
}
</script>
