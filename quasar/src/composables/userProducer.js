import { computed } from "vue"
import { useStore } from "vuex"

export const useUserProducer = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user.data)

	const isUserHavingProducerRight = (rightId) => {
		return user.value.producers.find(
			(producer) => producer.pivot.user_id === user.value.id &&
				producer.pivot.rights.includes(rightId)
		)
	}

	return { isUserHavingProducerRight }
}
