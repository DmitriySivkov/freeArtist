import Error404 from "src/pages/Error404.vue"
import MainLayout from "src/layouts/MainLayout.vue"
import PersonalProducerProductLayout from "src/layouts/PersonalProducerProductLayout.vue"

import common from "src/router/common"
import personal from "src/router/personal"
import auth from "src/router/auth"
import producer from "src/router/producer"
import cart from "src/router/cart"
import user from "src/router/user"

import ProductShow from "src/pages/personal/producer/ProductShow.vue"
import ProductCreate from "src/pages/personal/producer/ProductCreate.vue"

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
	{
		path: "/personal/producer/:producer_id/products",
		component: PersonalProducerProductLayout,
		children: [
			{
				name: "personal_producer_products_show",
				path: ":product_id",
				component: ProductShow,
			},
			{
				name: "personal_producer_products_create",
				path: "create",
				component: ProductCreate,
			},
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
