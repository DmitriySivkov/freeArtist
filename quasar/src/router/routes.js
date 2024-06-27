import common from "src/router/common"
import personal from "src/router/personal"
import auth from "src/router/auth"
import producer from "src/router/producer"
import cart from "src/router/cart"
import user from "src/router/user"
import checkout from "src/router/checkout"

const routes = [
	{
		path: "/",
		component: () => import("layouts/MainLayout.vue"),
		children: [
			...common,
			...auth,
			...personal,
			...producer,
			...cart,
			...user,
			...checkout
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
