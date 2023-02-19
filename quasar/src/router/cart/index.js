export default [
	{
		name: "cart",
		path: "cart",
		component: () => import("pages/Cart.vue"),
		meta: {
			route_name: "Корзина",
		},
	},
]
