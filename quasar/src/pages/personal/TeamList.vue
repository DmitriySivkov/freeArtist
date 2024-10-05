<script setup>
import { computed } from "vue"
import { orderBy, capitalize } from "lodash"
import { useRouter } from "vue-router"
import { useUserStore } from "src/stores/user"
import { TEAM_ENTITIES } from "src/const/teamEntities"

const props = defineProps({
	entity: String,
	navigationRouteName: String,
})

const $router = useRouter()

const userStore = useUserStore()

const teams = computed(() => orderBy(
	userStore.teams.filter((t) =>
		t.detailed_type === `App\\Models\\${capitalize(props.entity)}`
	),
	team => team.display_name,
	"asc"
))

const getNavigationRouteParams = (team) => {
	if (props.entity === TEAM_ENTITIES.PRODUCER) {
		return { producer_id: team.detailed_id }
	}

	if (props.entity === TEAM_ENTITIES.TEAM) {
		return { team_id: team.id }
	}
}

const goToDetail = (team) => {
	$router.push({
		name: props.navigationRouteName,
		params: getNavigationRouteParams(team)
	})
}
</script>

<template>
	<q-page class="row justify-center">
		<div class="col-xs-12 col-sm-8 col-md-7 col-lg-6 col-xl-5 q-pa-sm bg-white">
			<q-list separator>
				<q-item
					v-for="(team, index) in teams"
					:key="index"
					clickable
					class="bg-secondary text-h6 text-white rounded-borders"
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
