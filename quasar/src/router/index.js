import {route} from "quasar/wrappers"
import {createMemoryHistory, createRouter, createWebHashHistory, createWebHistory} from "vue-router"
import routes from "src/router/routes"
import { scroll } from "quasar"

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default route( async ({ store, ssrContext }) => {
	const createHistory = process.env.SERVER
		? createMemoryHistory
		: (process.env.VUE_ROUTER_MODE === "history" ? createWebHistory : createWebHashHistory)

	return createRouter({
		routes,

		// Leave this as is and make changes in quasar.conf.js instead!
		// quasar.conf.js -> build -> vueRouterMode
		// quasar.conf.js -> build -> publicPath
		history: createHistory(process.env.MODE === "ssr" ? void 0 : process.env.VUE_ROUTER_BASE)
	})
})
