import { useUserStore } from "src/stores/user"
export const useUser = () => {
	const user_store = useUserStore()

	const isUserLogged = () => user_store.is_logged

	return {
		isUserLogged
	}
}
