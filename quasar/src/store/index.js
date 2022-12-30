import { store } from "quasar/wrappers"
import { createStore } from "vuex"
import user from "src/store/user"
import role from "src/store/role"
import order from "src/store/order"
import producer from "src/store/producer"
import cart from "src/store/cart"
import relationRequest from "src/store/relationRequest"
import userProducer from "src/store/userProducer"
import team from "src/store/team"
import permission from "src/store/permission"

// import example from './module-example'

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default store(function (/* { ssrContext } */) {
	return createStore({
		modules: {
			user,
			role,
			permission,
			order,
			producer,
			cart,
			relationRequest,
			userProducer,
			team
		},

		// enable strict mode (adds overhead!)
		// for dev mode and --debug builds only
		strict: process.env.DEBUGGING
	})
})
