import { echo } from "boot/ws"
import { useUserProducer } from "src/composables/userProducer"
import { useUserPermission } from "src/composables/userPermission"
import { useUser } from "src/composables/user"
import { useStore } from "vuex"

export const usePrivateChannels = () => {
	const $store = useStore()
	const { producerTeams, getProducer } = useUserProducer()
	const { hasPermission } = useUserPermission()
	const { user } = useUser()

	const connectRelationRequestUser = () => {
		// todo - set 'created' hook right
		echo.private("relation-requests.user." + user.value.data.id)
			.listen(".RelationRequestCreated", (e) => {

			})
			.listen(".RelationRequestUpdated", (e) => {
				$store.commit("user/SET_USER_OUTGOING_COWORKING_REQUEST_STATUS", {
					request_id: e.model.id,
					status: e.model.status
				})
				if (e.type === "coworking") {
					$store.commit("user/SET_ROLE", e.role)
					$store.commit("userProducer/SET_USER_PRODUCER", e.producer)
				}
			})
	}

	const connectRelationRequestIncomingProducer = () => {
		if (producerTeams.value.length > 0) {
			for (let i in producerTeams.value) {
				if (
					producerTeams.value[i].user_id === user.value.data.id ||
					hasPermission(producerTeams.value[i].id, ["producer_incoming_coworking_requests"])
				) {
					echo.private("relation-requests.incoming.producer." + producerTeams.value[i].id)
						.listen(".RelationRequestCreated", (e) => {
							$store.commit("userProducer/SET_PRODUCER_INCOMING_RELATION_REQUESTS", {
								incoming_coworking_requests: [e.model],
								producer_id: producerTeams.value[i].id
							})
						})
						.listen(".RelationRequestUpdated", (e) => {
							$store.commit("userProducer/SET_PRODUCER_INCOMING_COWORKING_REQUEST_STATUS", {
								producer_id: producerTeams.value[i].id,
								request_id: e.model.id,
								status: e.model.status
							})
						})
				}
			}
		}
	}

	const connectPermissions = () => {
		echo.private("permissions." + user.value.data.id)
			.listen(".permissions.synced", (e) => {
				$store.commit("user/SYNC_USER_TEAM_PERMISSIONS", {
					team_id: e.producer.id,
					permissions: e.permissions
				})
				if (getProducer(e.producer.id).hasOwnProperty("users")) {
					$store.commit("userProducer/SYNC_PRODUCER_USER_PERMISSIONS", {
						producer_id: e.producer.id,
						user_id: e.user.id,
						permissions: e.permissions
					})
				}
			})
	}

	return {
		connectRelationRequestUser,
		connectRelationRequestIncomingProducer,
		connectPermissions
	}
}
