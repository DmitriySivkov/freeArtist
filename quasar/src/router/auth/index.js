export default [
	{
		name: "login",
		path: "auth",
		component: () => import("pages/Auth.vue"),
		meta: {
			route_name: "Авторизация",
		}
	},
]
