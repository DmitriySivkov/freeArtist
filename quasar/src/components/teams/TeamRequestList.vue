<template>
	<div v-if="team.detailed_type === 'App\\Models\\Producer'">
		<!--					<OutgoingProducerRequests :team="team" />-->
		<IncomingProducerRequests :team="team" />
	</div>
</template>

<script>
// import OutgoingProducerRequests from "src/components/requests/producer/OutgoingProducerRequests"
import IncomingProducerRequests from "src/components/requests/producer/IncomingProducerRequests"

export default {
	props: {
		team: {
			type: Object,
			default: () => ({})
		}
	},
	components: {
		// OutgoingProducerCoworkingInvitations,
		IncomingProducerRequests
	},
}
</script>
