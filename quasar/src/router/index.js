import { route } from "quasar/wrappers"
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from "vue-router"
import routes from "src/router/routes"
import { Loading } from "quasar"

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

/** router inits before boot files -
 * 	because of that, loading user here
 *  https://quasar.dev/quasar-cli-webpack/boot-files#quasar-app-flow
 */

export default route( async ({ store, ssrContext }) => {
	Loading.show({
		spinnerColor: "primary",
	})

	/** loads user by cookie */
	await store.dispatch("user/checkTokenCookie")

	const createHistory = process.env.SERVER
		? createMemoryHistory
		: (process.env.VUE_ROUTER_MODE === "history" ? createWebHistory : createWebHashHistory)

	const Router = createRouter({
		scrollBehavior: () => ({ left: 0, top: 0 }),
		routes,

		// Leave this as is and make changes in quasar.conf.js instead!
		// quasar.conf.js -> build -> vueRouterMode
		// quasar.conf.js -> build -> publicPath
		history: createHistory(process.env.MODE === "ssr" ? void 0 : process.env.VUE_ROUTER_BASE)
	})

	Router.beforeEach((to, from, next) => {
		if (to.meta.requires_auth && !store.state.user.isLogged) next({ name: "home" })
		if (to.name === "login" && store.state.user.isLogged) next({name: "home"})
		else next()
	})

	Loading.hide()

	return Router
})
