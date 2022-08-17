<template>
	<ProducerTeamList
		detail-route-name="personal_producer_team_permissions"
		:teams="producerTeamsSorted"
	/>
</template>

<script>
import ProducerTeamList from "src/components/producers/ProducerTeamList"
import { computed } from "vue"
import _ from "lodash"
import { useUserProducer } from "src/composables/userProducer"

export default {
	components: { ProducerTeamList },
	setup() {
		const { producerTeams } = useUserProducer()
		const producerTeamsSorted = computed(() => _.orderBy(
			producerTeams.value, team => team.display_name, "asc"
		))

		return {
			producerTeamsSorted
		}
	}
}
</script>
