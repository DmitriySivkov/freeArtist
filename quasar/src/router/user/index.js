export default [
	{
		name: "user_orders",
		path: "/orders",
		component: () => import("pages/user/Orders.vue"),
		meta: {
			route_name: "Список заказов"
		}
	},
]
