import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const team_store = computed(() => $store.state.team)
	const relation_request = computed(() => $store.state.relation_request)

	const teamIncomingRequests = (team) =>
		relation_request.value.user_teams_requests.filter(
			(r) => r.to_id === team.detailed_id && r.to_type === team.detailed_type
		)

	const userCreateRequest = (team, message) =>
		$store.dispatch("user/createRequest",{ team, message })

	const userCancelRequest = (requestId) =>
		$store.dispatch("user/cancelRequest", {
			requestId,
			status: relation_request.value.statuses.rejected_by_contributor
		})

	const userRestoreRequest = (requestId) =>
		$store.dispatch("user/restoreRequest", {
			requestId,
			status: relation_request.value.statuses.pending
		})

	const user_outgoing_requests = computed(() =>
		relation_request.value.user_requests.filter(
			(r) => r.from_id === user.value.data.id && r.from_type === "App\\Models\\User"
		)
	)

	const teamPendingRequestCount = (team) =>
		teamIncomingRequests(team).filter((r) => r.status.id === relation_request.value.statuses.pending.id).length

	// todo - rework for not only producer
	const teamAcceptRequest = (team_id, request_id) =>
		$store.dispatch("team/acceptRequest", { team_id, request_id })

	// todo - rework for not only producer
	const teamRejectRequest = (team_id, request_id) =>
		$store.dispatch("team/rejectRequest", { team_id, request_id })

	return {
		relation_request,
		userCreateRequest,
		userCancelRequest,
		userRestoreRequest,
		teamIncomingRequests,
		acceptCoworkingRequest,
		rejectCoworkingRequest,
		teamPendingRequestCount,
		user_outgoing_requests
	}
}
