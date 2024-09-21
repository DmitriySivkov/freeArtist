<script setup>
import { computed, onBeforeMount } from "vue"
import { orderBy } from "lodash"
import { useTeamStore } from "src/stores/team"
import { useRouter } from "vue-router"
import { useUserStore } from "src/stores/user"

const props = defineProps({
	navigationRouteName: {
		type: String,
		required: false
	}
})

const $router = useRouter()

const userStore = useUserStore()
const teamStore = useTeamStore()

const personalTypes = {
	producer: "App\\Models\\Producer"
}

const teams = computed(() => orderBy(
	teamStore.user_teams.filter((t) =>
		t.detailed_type === personalTypes[userStore.personal_tab]
	),
	team => team.display_name,
	"asc"
))

const goToDetail = (team) => {
	$router.push({
		name: props.navigationRouteName,
		params: {
			team_id: team.id
		}
	})
}

onBeforeMount(() => {
	if (!props.navigationRouteName) {
		$router.replace({ name: "personal" })
	}
})
</script>

<template>
	<q-page class="row bg-primary">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 q-pa-sm">
			<q-list
				bordered
				separator
			>
				<q-item
					v-for="(team, index) in teams"
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
		</div>
	</q-page>
</template>
