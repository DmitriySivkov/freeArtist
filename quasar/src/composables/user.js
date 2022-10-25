import { computed } from "vue"
import { useStore } from "vuex"

export const useUser = () => {
	const $store = useStore()
	const user = computed(() => $store.state.user)

	const isUserLogged = () => user.value.isLogged

	return { user, isUserLogged }
}
