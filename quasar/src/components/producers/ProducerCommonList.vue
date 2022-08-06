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
				@click="goToRoute(routeName, team.id)"
			>
				<q-item-section>
					{{ team.display_name }}
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
	props: {
		routeName: String,
	},
	setup() {
		const $router = useRouter()
		const { producerTeams } = useUserProducer()
		const producerTeamsSorted = computed(() => _.orderBy(
			producerTeams.value, team => team.display_name, "asc"
		))
		const goToRoute = (route_name, team_id) => $router.push({name:route_name, params: { id:team_id } })

		return {
			producerTeamsSorted,
			goToRoute
		}
	}
})
</script>
