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

	return { producerTeams, userOwnProducer }
}
