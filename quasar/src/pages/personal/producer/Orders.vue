<script setup>
import TeamList from "src/components/teams/TeamList.vue"
import { computed } from "vue"
import { orderBy } from "lodash"
import { useUserTeam } from "src/composables/userTeam"

const { user_teams } = useUserTeam()
const teams = computed(() => orderBy(
	user_teams.value,
	team => team.display_name,
	"asc"
))
</script>

<template>
	<q-page class="row bg-primary">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 q-pa-sm">
			<TeamList
				detail-route-name="personal_producer_orders_detail"
				:teams="teams"
			/>
		</div>
	</q-page>
</template>
