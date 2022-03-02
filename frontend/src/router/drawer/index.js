export default [
	{
		path: "",
		component: () => import("pages/Producers"),
		meta: {
			route_name: "Главная"
		}
	},
	{
		path: "auth",
		component: () => import("pages/Auth"),
		meta: {
			route_name: "Войти"
		}
	},
]
