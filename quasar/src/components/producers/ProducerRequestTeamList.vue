<template>
	<div class="q-pa-md">
		<q-list
			bordered
			separator
		>
			<q-item
				v-for="(team, index) in producerTeamsSorted"
				:key="index"
				clickable
				v-ripple
				@click="goToTeamRequests(team.id)"
			>
				<q-item-section>
					{{ team.display_name }}
				</q-item-section>
				<q-item-section side>
					{{ team.requests.total_request_count }}
				</q-item-section>
			</q-item>
		</q-list>
	</div>
</template>

<script>
import _ from "lodash"
import { computed } from "vue"
import { useRouter } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"

export default ({
	setup() {
		const $router = useRouter()
		const { producerTeams } = useUserProducer()
		const producerTeamsSorted = computed(() => _.orderBy(
			producerTeams.value, team => team.display_name, "asc"
		))

		const goToTeamRequests = (team_id) => $router.push({
			name:"personal_producer_team_requests",
			params: {
				id:team_id
			}
		})

		return {
			producerTeamsSorted,
			goToTeamRequests
		}
	}
})
</script>
