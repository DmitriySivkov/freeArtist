<template>
	<ProducerRequestList :producer="team"/>
</template>

<script>
import ProducerRequestList from "src/components/producers/ProducerRequestList"
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed } from "vue"

export default {
	components: { ProducerRequestList },
	setup() {
		const $route = useRoute()
		const { producerTeams } = useUserProducer()
		const team = computed(() =>
			producerTeams.value.find((team) => team.id === parseInt($route.params.id))
		)

		return {
			team
		}
	}
}
</script>
