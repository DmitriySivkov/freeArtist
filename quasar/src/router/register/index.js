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
		name: "register_user",
		path: "register/user",
		component: () => import("components/register/CustomerRegister"),
		meta: {
			route_name: "Регистрация: пользователь",
			role_id: 1
		}
	},
	{
		name: "register_producer",
		path: "register/producer",
		component: () => import("components/register/ProducerRegister"),
		meta: {
			route_name: "Регистрация: мастер",
			role_id: 2
		}
	},
]
