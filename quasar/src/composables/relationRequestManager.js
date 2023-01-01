import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const team_store = computed(() => $store.state.team)
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
		team_store.value.user_teams.find((team) => team.id === producerId)
			.requests
			.data
			.outgoing_producer_partnership_requests

	const incomingProducerPartnershipRequests = (producerId) =>
		team_store.value.user_teams.find((team) => team.id === producerId)
			.requests
			.data
			.incoming_producer_partnership_requests

	const incomingCoworkingRequests = (producerId) =>
		team_store.value.user_teams.find((team) => team.id === producerId)
			.requests
			.data
			.incoming_coworking_requests

	const producerPendingRequestCount = (team) =>
		Object.keys(team.requests.data).reduce((carry, requestType) =>
			carry + team.requests.data[requestType].filter(
				(request) => request.status.id === relationRequest.value.statuses.pending.id
			).length, 0
		)

	const acceptCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("producer/acceptCoworkingRequest", { producer_id, request_id })

	const rejectCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("producer/rejectCoworkingRequest", { producer_id, request_id })

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
		rejectCoworkingRequest,
		producerPendingRequestCount
	}
}
