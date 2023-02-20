<template>
	<div class="q-pa-md">
		<q-list
			bordered
			separator
		>
			<q-item
				v-for="(team, index) in teams_filtered"
				:key="index"
				clickable
				@click="goToDetail(team)"
			>
				<q-item-section>
					{{ team.display_name }}
				</q-item-section>
				<q-item-section
					v-if="useCounter"
					side
				>
					{{ counter(team) }}
				</q-item-section>
			</q-item>
		</q-list>
	</div>
</template>

<script>
import { useRouter } from "vue-router"
import { computed } from "vue"
import { useUserStore } from "src/stores/user"
export default ({
	props: {
		detailRouteName: String,
		teams: {
			type: Array,
			default: () => []
		},
		counter: Function,
		useCounter: {
			type: Boolean,
			default: false
		}
	},
	setup(props) {
		const user_store = useUserStore()
		const $router = useRouter()

		const personal_types = {
			producer: "App\\Models\\Producer"
		}

		const teams_filtered = computed(
			() => props.teams.filter((t) => t.detailed_type === personal_types[user_store.personal_tab])
		)

		const goToDetail = (team) => {
			let detailed_route_param = { team_id: team.id }

			if (props.detailRouteName.includes("_producer_"))
				detailed_route_param = { producer_id: team.detailed.id}

			$router.push({
				name: props.detailRouteName,
				params: {
					...detailed_route_param
				}
			})
		}

		return {
			goToDetail,
			teams_filtered
		}
	}
})
</script>
