<template>
	<ProducerProfile :producer="team"/>
</template>

<script>
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed } from "vue"
import ProducerProfile from "src/components/producers/ProducerProfile"
export default {
	components: { ProducerProfile },
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.team_id))
		)

		return {
			team
		}
	}
}
</script>
