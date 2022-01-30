export default [
	{
		path: "personal",
		component: () => import("pages/Personal"),
		meta: {
			route_name: "Личный кабинет"
		},
	},
	{
		path: "personal/orders",
		component: () => import("pages/Orders"),
		meta: {
			route_name: "Список заказов"
		}
	},
	{
		path: "personal/user",
		component: () => import("pages/User"),
		meta: {
			route_name: "Персональные данные"
		}
	},
]
