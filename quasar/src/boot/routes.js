import { Loading } from "quasar"
import { useUserStore } from "src/stores/user"

export default ({ store, router }) => {
	const user_store = useUserStore(store)

	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !user_store.is_logged)
			next({ name: "home" })

		next()
	})

	Loading.hide()
}
