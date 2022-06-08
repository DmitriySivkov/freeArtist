export default [
	{
		name: "personal",
		path: "personal",
		component: () => import("pages/personal/Navigation"),
		meta: {
			route_name: "Личный кабинет",
			caption: "",
			icon: "account_circle",
			show_in_drawer: true,
			requires_auth: true
		},
	},
	{
		name: "personal_orders",
		path: "personal/orders",
		component: () => import("pages/personal/Orders"),
		meta: {
			route_name: "Список заказов",
			requires_auth: true,
		}
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: () => import("pages/personal/User"),
		meta: {
			route_name: "Персональные данные",
			requires_auth: true
		}
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: () => import("pages/personal/RegisterProducer"),
		meta: {
			route_name: "Зарегистрировать изготовителя",
			requires_auth: true
		}
	},
	{
		name: "personal_join_producer",
		path: "personal/join-producer",
		component: () => import("pages/personal/JoinProducer"),
		meta: {
			route_name: "Заявка: присоединиться к изготовителю",
			requires_auth: true
		}
	},
	{
		name: "personal_requests",
		path: "personal/requests",
		component: () => import("pages/personal/Requests"),
		meta: {
			route_name: "Заявки",
			requires_auth: true
		}
	}
]
