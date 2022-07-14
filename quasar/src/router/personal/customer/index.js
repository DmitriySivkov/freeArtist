export default [
	{
		name: "personal_user_orders",
		path: "personal/user/orders",
		component: () => import("pages/personal/customer/Orders"),
		meta: {
			route_name: "Список заказов",
			requires_auth: true,
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
		name: "personal_coworking_request",
		path: "personal/coworking-request",
		component: () => import("pages/personal/CoworkingRequest"),
		meta: {
			route_name: "Заявка: присоединиться к изготовителю",
			requires_auth: true
		}
	},
	{
		name: "personal_user_requests",
		path: "personal/user/requests",
		component: () => import("pages/personal/customer/Requests"),
		meta: {
			route_name: "Заявки",
			requires_auth: true
		}
	},
]
