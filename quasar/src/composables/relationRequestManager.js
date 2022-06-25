import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const relationRequest = computed(() => $store.state.relationRequest)

	const sendCoworkingRequest = (producer, message) =>
		$store.dispatch("user/sendCoworkingRequest",{ producer, message })

	const cancelCoworkingRequest = (requestId) => {
		return $store.dispatch("user/cancelCoworkingRequest", {
			requestId,
			status: relationRequest.value.statuses.rejected_by_contributor
		})
	}

	const restoreCoworkingRequest = (requestId) => {
		return $store.dispatch("user/restoreCoworkingRequest", {
			requestId,
			status: relationRequest.value.statuses.pending
		})
	}

	const outgoingCoworkingRequests = user.value.data.outgoing_coworking_requests

	return {
		relationRequest,
		outgoingCoworkingRequests,
		cancelCoworkingRequest,
		sendCoworkingRequest,
		restoreCoworkingRequest
	}
}
