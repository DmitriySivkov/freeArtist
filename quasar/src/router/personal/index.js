import customerRoutes from "src/router/personal/customer"
import producerRoutes from "src/router/personal/producer"

export default [
	...customerRoutes,
	...producerRoutes,
	{
		name: "personal",
		path: "personal",
		component: () => import("pages/personal/Index"),
		meta: {
			route_name: "Личный кабинет",
		},
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: () => import("pages/personal/User"),
		meta: {
			route_name: "Персональные данные"
		}
	}
]
