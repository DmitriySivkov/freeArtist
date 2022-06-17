export default [
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
		name: "personal_producer_requests",
		path: "personal/producer/requests",
		component: () => import("pages/personal/producer/Requests"),
		meta: {
			route_name: "Заявки",
			requires_auth: true
		}
	},
]
