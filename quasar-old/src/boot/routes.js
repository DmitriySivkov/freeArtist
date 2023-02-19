import { Loading } from "quasar"

export default ({ store, router }) => {
	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !store.state.user.is_logged)
			next({ name: "home" })

		next()
	})
	Loading.hide()
}
