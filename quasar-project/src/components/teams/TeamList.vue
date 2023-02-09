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
import { useStore } from "vuex"
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
		const $store = useStore()
    const user_store = useUserStore()
		const $router = useRouter()

		const user = computed(() => user_store.$state)

		const personal_types = {
			producer: "App\\Models\\Producer"
		}

		const personal_types_to_detail = {
			"App\\Models\\Producer": "producer_id"
		}

		const teams_filtered = computed(
			() => props.teams.filter((t) => t.detailed_type === personal_types[user.value.personal_tab])
		)

		const goToDetail = (team) => $router.push({
			name: props.detailRouteName,
			params: { team_id: team.id, [personal_types_to_detail[team.detailed_type]]: team.detailed.id }
		})

		return {
			goToDetail,
			teams_filtered
		}
	}
})
</script>
