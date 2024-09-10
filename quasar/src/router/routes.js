import Error404 from "src/pages/Error404.vue"
import MainLayout from "src/layouts/MainLayout.vue"

import common from "src/router/common"
import personal from "src/router/personal"
import auth from "src/router/auth"
import producer from "src/router/producer"
import cart from "src/router/cart"
import user from "src/router/user"

const routes = [
	{
		path: "/",
		component: MainLayout,
		children: [
			...common,
			...auth,
			...personal,
			...producer,
			...cart,
			...user,
		]
	},

	// Always leave this as last one,
	// but you can also remove it
	{
		path: "/:catchAll(.*)*",
		component: Error404
	}
]

export default routes
