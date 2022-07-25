import { computed } from "vue"
import { useStore } from "vuex"

export const useUserProducer = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user.data)
	const roleTypes = computed(() => $store.state.role.types)

	const producerTeams = computed(() =>
		user.value.teams.filter((team) => team.detailed_type === roleTypes.value.producer))

	const userOwnProducer = computed(() =>
		producerTeams.value.find((team) => team.user_id === user.value.id))

	return { producerTeams, userOwnProducer }
}
