import Register from "src/pages/Register.vue"
import Auth from "src/pages/Auth.vue"

export default [
	{
		name: "register",
		path: "register",
		component: Register,
	},
	{
		name: "login",
		path: "auth",
		component: Auth,
	},
]
