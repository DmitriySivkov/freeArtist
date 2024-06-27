export default [
	{
		name: "checkout",
		path: "checkout/:producerId",
		component: () => import("pages/Checkout.vue"),
		meta: {
			route_name: "Оформление заказа",
		},
	},
]
