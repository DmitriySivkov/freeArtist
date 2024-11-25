import { useUserStore } from "src/stores/user"
import { scroll } from "quasar"

export default ({ store, router }) => {
	const userStore = useUserStore(store)

	const { setVerticalScrollPosition, getScrollTarget } = scroll

	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !userStore.is_logged) {
			next({
				name: "home"
			})

			return
		}

		if (from.name === "home") {
			// reset scroll to avoid height jump consequences when navigating from scrolled home
			setVerticalScrollPosition(getScrollTarget(null), 0)
		}

		next()
	})
}
