import { echo } from "src/boot/ws"
import { useUserStore } from "src/stores/user"
import { useProducerOrdersStore } from "src/stores/producerOrders"

export const usePrivateChannels = () => {
	const userStore = useUserStore()
	const producerOrderStore = useProducerOrdersStore()

	const connectTeams = () => {
		if (userStore.teams.length === 0)
			return

		for (let i in userStore.teams) {
			echo.private("teams." + userStore.teams[i].id)
				.listen(".teams.updated", (e) => {
					userStore.setTeamFields({
						teamId: e.teamId,
						fields: e.fields,
						detailedId: e.detailedId
					})
				})
		}

	}

	const connectPermissions = () => {
		if (userStore.teams.length === 0)
			return

		// todo - only listen to auth user permissions: check on backend
		for (let i in userStore.teams) {
			echo.private("permissions." + userStore.teams[i].id)
				.listen(".permissions.synced", (e) => {
					userStore.setTeamFields({
						teamId: e.team.id,
						fields: {
							permissions: e.permissions
						},
					})
				})
		}
	}

	const connectRelationRequests = () => {
		echo.private(`relation-requests.user.${userStore.data.id}`)
			.listen(".user-team.relation-request.accepted", (e) => {
				userStore.setTeams(e.team)
			})
	}

	const connectProducerOrders = () => {
		const userProducers = userStore.teams.filter((t) => t.detailed_type === "App\\Models\\Producer")

		if (userProducers.length === 0)
			return

		for (let i in userProducers) {
			echo.private(`producers.${userProducers[i].detailed_id}.orders`)
				.listen(".order.created", (e) => {
					producerOrderStore.commitData([e.model])
				})
				.listen(".order.updated", (e) => {
					producerOrderStore.commitOrderFields({
						orderId: e.model.id,
						fields: e.model,
					})
				})
		}
	}

	const disconnectProducerOrders = () => {
		const userProducers = userStore.teams.filter((t) => t.detailed_type === "App\\Models\\Producer")

		if (userProducers.length === 0)
			return

		for (let i in userProducers) {
			echo.leave(`producers.${userProducers[i].detailed_id}.orders`)
		}
	}

	return {
		connectTeams,
		connectPermissions,
		connectRelationRequests,
		connectProducerOrders,
		disconnectProducerOrders
	}
}
