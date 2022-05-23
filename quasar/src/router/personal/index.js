export default [
	{
		name: "personal",
		path: "personal",
		component: () => import("pages/personal/Navigation"),
		meta: {
			route_name: "Личный кабинет",
			caption: "",
			icon: "account_circle",
			show_in_drawer: true,
			requires_auth: true
		},
	},
	{
		name: "personal_orders",
		path: "personal/orders",
		component: () => import("pages/personal/Orders"),
		meta: {
			route_name: "Список заказов",
			requires_auth: true,
		}
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: () => import("pages/personal/User"),
		meta: {
			route_name: "Персональные данные",
			requires_auth: true
		}
	},
	// {
	// 	name: "personal_becomeProducer",
	// path: "personal/user",
	// component: () => import("pages/User"),
	// meta: {
	// 	route_name: "Персональные данные",
	// 	requires_auth: true
	// }
	// }
]
