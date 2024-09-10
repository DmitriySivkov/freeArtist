import Register from "src/pages/Register.vue"
import Auth from "src/pages/Auth.vue"

export default [
	{
		name: "register",
		path: "register",
		component: Register,
		meta: {
			route_name: "Регистрация"
		},
	},
	{
		name: "login",
		path: "auth",
		component: Auth,
		meta: {
			route_name: "Авторизация",
		}
	},
]
