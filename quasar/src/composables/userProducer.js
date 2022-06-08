import { computed } from "vue"
import { useStore } from "vuex"

export const useUserProducer = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user.data)

	const getUserProducerListByRight = (rightId) => {
		return user.value.producers.filter(
			(producer) => producer.pivot.user_id === user.value.id &&
				producer.pivot.rights.includes(rightId)
		)
	}

	return { getUserProducerListByRight }
}
