import { useUserStore } from "src/stores/user"

export default ({ store, router }) => {
	const userStore = useUserStore(store)

	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !userStore.is_logged) {
			next({
				name: "home"
			})

			return
		}

		next()
	})

	document.getElementById("blind").checked = true

	setTimeout(
		() => document.getElementById("loading-screen").remove(),
		2000
	)

}
