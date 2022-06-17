import { computed } from "vue"
import { useStore } from "vuex"

export const useRelationRequestManager = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const sendCoworkingRequest = (producer, message) =>
		$store.dispatch("user/sendCoworkingRequest",{ producer, message })

	const cancelCoworkingRequest = (requestId) => {
		if (
			user.value.data.outgoing_coworking_requests
				.find((request) => request.id === requestId)
				.status === 4
		)
			return Promise.reject("Заявка уже отменена")

		return $store.dispatch("user/cancelCoworkingRequest", requestId)
	}

	const outgoingCoworkingRequests = user.value.data.outgoing_coworking_requests

	return { outgoingCoworkingRequests, cancelCoworkingRequest, sendCoworkingRequest }
}
