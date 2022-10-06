<template>
	<!--					<OutgoingProducerPartnershipRequests :producer="props.row" />-->
	<!--					<IncomingProducerPartnershipRequests :producer="props.row" />-->
	<!--					<OutgoingProducerCoworkingInvitations :producer="props.row" />-->
	<IncomingProducerCoworkingRequests :producer="producer" />
</template>

<script>
// import OutgoingProducerPartnershipRequests from "src/components/requests/producer/OutgoingProducerPartnershipRequests"
// import IncomingProducerPartnershipRequests from "src/components/requests/producer/IncomingProducerPartnershipRequests"
// import OutgoingProducerCoworkingInvitations from "src/components/requests/producer/OutgoingProducerCoworkingInvitations"
import IncomingProducerCoworkingRequests from "src/components/requests/producer/IncomingProducerCoworkingRequests"

export default {
	props: {
		producer: {
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
