<template>
	<ProducerPermissionList :producer="team"/>
</template>

<script>
import ProducerPermissionList from "src/components/producers/ProducerPermissionList"
import { useRoute } from "vue-router"
import { useUserProducer } from "src/composables/userProducer"
import { computed } from "vue"

export default {
	// todo - detailed permissions
	components: { ProducerPermissionList },
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
