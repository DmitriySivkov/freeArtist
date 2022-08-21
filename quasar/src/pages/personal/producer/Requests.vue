<template>
	<ProducerTeamList
		detail-route-name="personal_producer_team_requests"
		:teams="producerTeamsSorted"
		:counter="producerPendingRequestCount"
		:use-counter="true"
	/>
</template>

<script>
import ProducerTeamList from "src/components/producers/ProducerTeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserProducer } from "src/composables/userProducer"
import { useRelationRequestManager } from "src/composables/relationRequestManager"

export default {
	components: { ProducerTeamList },
	setup() {
		const { producerPendingRequestCount } = useRelationRequestManager()
		const { producerTeams } = useUserProducer()
		const producerTeamsSorted = computed(() => _.orderBy(
			producerTeams.value, team => team.display_name, "asc"
		))

		return {
			producerTeamsSorted,
			producerPendingRequestCount
		}
	}
}
</script>
