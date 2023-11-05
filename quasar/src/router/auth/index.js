export default [
	{
		name: "register",
		path: "register",
		component: () => import("pages/Register.vue"),
		meta: {
			route_name: "Регистрация"
		},
	},
	{
		name: "login",
		path: "auth",
		component: () => import("pages/Auth.vue"),
		meta: {
			route_name: "Авторизация",
		}
	},
]
