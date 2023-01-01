<template>
	<ProducerTeamList
		detail-route-name="personal_producer_requests"
		:teams="producerTeamsSorted"
		:counter="producerPendingRequestCount"
		:use-counter="true"
	/>
</template>

<script>
import ProducerTeamList from "src/components/producers/ProducerTeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserTeam } from "src/composables/userTeam"
import { useRelationRequestManager } from "src/composables/relationRequestManager"
export default {
	components: { ProducerTeamList },
	setup() {
		const { producerPendingRequestCount } = useRelationRequestManager()
		const { user_teams } = useUserTeam()
		const producerTeamsSorted = computed(() => _.orderBy(
			user_teams.value.filter((t) => t.detailed_type === "App\\Models\\Producer"),
			team => team.display_name,
			"asc"
		))

		return {
			producerTeamsSorted,
			producerPendingRequestCount
		}
	}
}
</script>
