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

		if (
			[
				"personal_producer_products_detail_show",
				"personal_producer_products_detail_create"
			].includes(to.name) && !from.name
		) {
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

	document.getElementById("loading-screen").remove()
}
