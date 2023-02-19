export default [
	{
		name: "register",
		path: "register",
		component: () => import("pages/Register"),
		meta: {
			route_name: "Регистрация"
		},
	},
	{
		name: "login",
		path: "auth",
		component: () => import("pages/Auth"),
		meta: {
			route_name: "Авторизация",
		}
	},
]
