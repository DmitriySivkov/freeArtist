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
				@click="goToDetail(team.id)"
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
import { useStore } from "vuex"
import { computed } from "vue"
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
		const $store = useStore()
		const $router = useRouter()

		const user = computed(() => $store.state.user)

		const personal_types = {
			producer: "App\\Models\\Producer"
		}

		const teams_filtered = computed(
			() => props.teams.filter((t) => t.detailed_type === personal_types[user.value.personalTab])
		)

		const goToDetail = (team_id) => $router.push({
			name: props.detailRouteName,
			params: { team_id }
		})

		return {
			goToDetail,
			teams_filtered
		}
	}
})
</script>
