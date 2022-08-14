import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const userProducer = computed(() => $store.state.userProducer)
	const relationRequest = computed(() => $store.state.relationRequest)

	/** customer requests */
	const sendCoworkingRequest = (producer, message) =>
		$store.dispatch("user/sendCoworkingRequest",{ producer, message })

	const cancelCoworkingRequest = (requestId) =>
		$store.dispatch("user/cancelCoworkingRequest", {
			requestId,
			status: relationRequest.value.statuses.rejected_by_contributor
		})

	const restoreCoworkingRequest = (requestId) =>
		$store.dispatch("user/restoreCoworkingRequest", {
			requestId,
			status: relationRequest.value.statuses.pending
		})

	const outgoingCoworkingRequests = user.value.data.outgoing_coworking_requests

	/** producer requests */
	const outgoingProducerPartnershipRequests = (producerId) =>
		userProducer.value.producers.find((team) => team.id === producerId)
			.requests
			.outgoing_producer_partnership_requests

	const incomingProducerPartnershipRequests = (producerId) =>
		userProducer.value.producers.find((team) => team.id === producerId)
			.requests
			.incoming_producer_partnership_requests

	const incomingCoworkingRequests = (producerId) =>
		userProducer.value.producers.find((team) => team.id === producerId)
			.requests
			.incoming_coworking_requests

	const acceptCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("userProducer/acceptCoworkingRequest", { producer_id, request_id })

	const rejectCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("userProducer/rejectCoworkingRequest", { producer_id, request_id })

	return {
		relationRequest,
		outgoingCoworkingRequests,
		cancelCoworkingRequest,
		sendCoworkingRequest,
		restoreCoworkingRequest,
		outgoingProducerPartnershipRequests,
		incomingProducerPartnershipRequests,
		incomingCoworkingRequests,
		acceptCoworkingRequest,
		rejectCoworkingRequest
	}
}
