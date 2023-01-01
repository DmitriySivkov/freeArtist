<template>
	<!--					<OutgoingProducerPartnershipRequests :team="team" />-->
	<!--					<IncomingProducerPartnershipRequests :team="team" />-->
	<!--					<OutgoingProducerCoworkingInvitations :team="team" />-->
	<IncomingProducerCoworkingRequests :team="team" />
</template>

<script>
// import OutgoingProducerPartnershipRequests from "src/components/requests/producer/OutgoingProducerPartnershipRequests"
// import IncomingProducerPartnershipRequests from "src/components/requests/producer/IncomingProducerPartnershipRequests"
// import OutgoingProducerCoworkingInvitations from "src/components/requests/producer/OutgoingProducerCoworkingInvitations"
import IncomingProducerCoworkingRequests from "src/components/requests/producer/IncomingProducerCoworkingRequests"

export default {
	props: {
		team: {
			type: Object,
			default: () => ({})
		}
	},
	components: {
		// OutgoingProducerPartnershipRequests,
		// IncomingProducerPartnershipRequests,
		// OutgoingProducerCoworkingInvitations,
		IncomingProducerCoworkingRequests
	},
}
</script>
