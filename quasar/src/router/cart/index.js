export default [
	{
		name: "cart",
		path: "cart",
		component: () => import("pages/Cart"),
		meta: {
			route_name: "Корзина",
			caption: "",
			icon: "shopping_cart",
			show_in_drawer: true,
		},
	},
]
