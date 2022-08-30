<template>
	<div class="q-pa-md">
		<q-list
			bordered
			separator
		>
			<q-item
				v-for="(team, index) in teams"
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
		const $router = useRouter()

		const goToDetail = (team_id) => $router.push({
			name: props.detailRouteName,
			params: { team_id }
		})

		return {
			goToDetail
		}
	}
})
</script>
