import { computed } from "vue"
import { useUserStore } from "src/stores/user"
import { useRelationRequestStore } from "src/stores/relation-request"
import { useTeamStore } from "src/stores/team"

export const useRelationRequestManager = () => {
  const user_store = useUserStore()
  const relation_request_store = useRelationRequestStore()
  const team_store = useTeamStore()

	const teamIncomingRequests = (team) =>
		relation_request_store.user_teams_requests.filter(
			(r) => r.to_id === team.detailed_id && r.to_type === team.detailed_type
		)

	const userCreateRequest = (team_id, message) =>
		user_store.createRequest({ team_id, message })

	const userSetRequestStatus = (request_id, status_id) =>
		user_store.setRelationRequestStatus({
			request_id,
			status_id
		})

	const user_outgoing_requests = computed(() =>
		relation_request_store.user_requests.filter(
			(r) => r.from_id === user_store.data.id && r.from_type === "App\\Models\\User"
		)
	)

	const teamPendingRequestCount = (team) =>
		teamIncomingRequests(team).filter((r) => r.status.id === relation_request_store.statuses.pending.id).length

	const teamAcceptRequest = (team_id, request_id) =>
    team_store.acceptRequest({ team_id, request_id })

	const teamRejectRequest = (team_id, request_id) =>
    team_store.rejectRequest({ team_id, request_id })

	return {
		userCreateRequest,
		userSetRequestStatus,
		teamIncomingRequests,
		teamAcceptRequest,
		teamRejectRequest,
		teamPendingRequestCount,
		user_outgoing_requests
	}
}
