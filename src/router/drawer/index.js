export default [
	{
		path: "",
		component: () => import("pages/Index"),
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
