import { computed } from "vue"
import { useUserStore } from "src/stores/user"
export const useUser = () => {
	const user_store = useUserStore()
	const user = computed(() => user_store.$state)

	const isUserLogged = () => user.value.is_logged

	return {
		user,
		isUserLogged
	}
}
