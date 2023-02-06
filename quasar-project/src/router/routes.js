import common from "src/router/common"
import personal from "src/router/personal"
import auth from "src/router/auth"
import producer from "src/router/producer"
import cart from "src/router/cart"

const routes = [
	{
		path: "/",
		component: () => import("layouts/MainLayout.vue"),
		children: [
			...common,
			...auth,
			...personal,
			...producer,
			...cart
		]
	},

	// Always leave this as last one,
	// but you can also remove it
	{
		path: "/:catchAll(.*)*",
		component: () => import("pages/Error404.vue")
	}
]

export default routes
