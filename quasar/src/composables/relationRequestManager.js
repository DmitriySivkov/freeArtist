import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const relation_request = computed(() => $store.state.relation_request)

	const teamIncomingRequests = (team) =>
		relation_request.value.user_teams_requests.filter(
			(r) => r.to_id === team.detailed_id && r.to_type === team.detailed_type
		)

	const userCreateRequest = (team_id, message) =>
		$store.dispatch("user/createRequest",{ team_id, message })

	const userSetRequestStatus = (request_id, status_id) =>
		$store.dispatch("user/setRelationRequestStatus", {
			request_id,
			status_id
		})

	const user_outgoing_requests = computed(() =>
		relation_request.value.user_requests.filter(
			(r) => r.from_id === user.value.data.id && r.from_type === "App\\Models\\User"
		)
	)

	const teamPendingRequestCount = (team) =>
		teamIncomingRequests(team).filter((r) => r.status.id === relation_request.value.statuses.pending.id).length

	const teamAcceptRequest = (team_id, request_id) =>
		$store.dispatch("team/acceptRequest", { team_id, request_id })

	const teamRejectRequest = (team_id, request_id) =>
		$store.dispatch("team/rejectRequest", { team_id, request_id })

	return {
		relation_request,
		userCreateRequest,
		userSetRequestStatus,
		teamIncomingRequests,
		teamAcceptRequest,
		teamRejectRequest,
		teamPendingRequestCount,
		user_outgoing_requests
	}
}
