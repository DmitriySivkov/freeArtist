import { Loading } from "quasar"
import { useUserStore } from "src/stores/user"

export default ({ store, router }) => {
	const user_store = useUserStore(store)

	router.beforeEach((to, from, next) => {
		if (to.path.includes("personal") && !user_store.is_logged) {
			next({
				name: "home"
			})

			return
		}

		if (to.name === "personal_producer_products_detail_show" && !from.name) {
			next({
				name: "personal_producer_products_detail",
				params: {
					producer_id: to.params.producer_id
				},
				query: {
					force_load: 1
				}
			})

			return
		}

		next()
	})

	Loading.hide()
}
