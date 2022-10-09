import { computed } from "vue"
import { useStore } from "vuex"

export const useUserProducer = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)
	const userProducer = computed(() => $store.state.userProducer)

	const producerTeams = computed(() => userProducer.value.producers)

	const userOwnProducer = computed(() =>
		producerTeams.value.find((team) => team.user_id === user.value.data.id)
	)

	const getProducer = (producer_id) =>
		producerTeams.value.find((team) => team.id === producer_id)

	const getProducerUser = (producer_id, user_id) =>
		producerTeams.value.find((team) => team.id === producer_id)
			.users
			.find((user) => user.id === user_id)

	const syncProducerUserPermissions = (producer_id, user_id, permissions) =>
		$store.dispatch(
			"userProducer/syncProducerUserPermissions",
			{ producer_id, user_id, permissions }
		)

	return {
		producerTeams,
		userOwnProducer,
		getProducer,
		getProducerUser,
		syncProducerUserPermissions
	}
}
