export default [
	{
		path: "register",
		component: () => import("pages/Register"),
		meta: {
			route_name: "Регистрация"
		},
	},
	{
		path: "register/user",
		component: () => import("components/register/UserRegister"),
		meta: {
			route_name: "Регистрация: пользователь",
			role_id: 1
		}
	},
	{
		path: "register/producer",
		component: () => import("components/register/ProducerRegister"),
		meta: {
			route_name: "Регистрация: мастер",
			role_id: 2
		}
	},
]
