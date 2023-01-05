import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const team_store = computed(() => $store.state.team)
	const relation_request = computed(() => $store.state.relation_request)

	const createUserOutgoingRequest = (team, message) =>
		$store.dispatch("user/createOutgoingRequest",{ team, message })

	// todo - rework - just accept, not specifically coworking
	const cancelCoworkingRequest = (requestId) =>
		$store.dispatch("user/cancelCoworkingRequest", {
			requestId,
			status: relation_request.value.statuses.rejected_by_contributor
		})

	// todo - rework - just accept, not specifically coworking
	const restoreCoworkingRequest = (requestId) =>
		$store.dispatch("user/restoreCoworkingRequest", {
			requestId,
			status: relation_request.value.statuses.pending
		})

	const teamIncomingRequests = (team) =>
		relation_request.value.user_teams_requests.filter(
			(r) => r.to_id === team.detailed_id && r.to_type === team.detailed_type
		)

	const user_outgoing_requests = computed(() =>
		relation_request.value.user_requests.filter(
			(r) => r.from_id === user.value.data.id && r.from_type === "App\\Models\\User"
		)
	)

	const teamPendingRequestCount = (team) =>
		teamIncomingRequests(team).filter((r) => r.status.id === relation_request.value.statuses.pending.id).length

	// todo - rework for not only producer
	const acceptCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("producer/acceptCoworkingRequest", { producer_id, request_id })

	// todo - rework for not only producer
	const rejectCoworkingRequest = (producer_id, request_id) =>
		$store.dispatch("producer/rejectCoworkingRequest", { producer_id, request_id })

	return {
		relation_request,
		cancelCoworkingRequest,
		createUserOutgoingRequest,
		restoreCoworkingRequest,
		teamIncomingRequests,
		acceptCoworkingRequest,
		rejectCoworkingRequest,
		teamPendingRequestCount,
		user_outgoing_requests
	}
}
