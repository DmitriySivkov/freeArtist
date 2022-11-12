export default ({ store, router }) => {
	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !store.state.user.isLogged)
			next({ name: "home" })

		next()
	})
}
