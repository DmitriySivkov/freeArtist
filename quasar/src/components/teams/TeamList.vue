<template>
	<q-list
		bordered
		separator
	>
		<q-item
			v-for="(team, index) in teamsFiltered"
			:key="index"
			clickable
			class="bg-grey-4 text-h6"
			@click="goToDetail(team)"
		>
			<q-item-section>
				{{ team.display_name }}
			</q-item-section>
		</q-item>
	</q-list>
</template>

<script setup>
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUserStore } from "src/stores/user"

const props = defineProps({
	detailRouteName: String,
	teams: {
		type: Array,
		default: () => []
	}
})

const userStore = useUserStore()
const $router = useRouter()

const personalTypes = {
	producer: "App\\Models\\Producer"
}

const teamsFiltered = computed(() =>
	props.teams.filter((t) => t.detailed_type === personalTypes[userStore.personal_tab])
)

const goToDetail = (team) => {
	let detailedRouteParam = { team_id: team.id }

	if (props.detailRouteName.includes("_producer_")) {
		detailedRouteParam = { producer_id: team.detailed.id }
	}

	$router.push({
		name: props.detailRouteName,
		params: {
			...detailedRouteParam
		}
	})
}
</script>
