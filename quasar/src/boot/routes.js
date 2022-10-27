export default ({ store, router }) => {
	router.beforeEach((to, from, next) => {
		if (
			(to.meta.requires_auth && !store.state.user.isLogged) ||
			(to.name === "login" && store.state.user.isLogged)
		)
			next({ name: "home" })

		next()
	})
}
