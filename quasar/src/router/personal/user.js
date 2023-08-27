export default [
	{
		name: "personal_user_orders",
		path: "personal/user/orders",
		component: () => import("pages/personal/user/Orders.vue"),
		meta: {
			route_name: "Список заказов"
		}
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: () => import("pages/personal/RegisterProducer.vue"),
		meta: {
			route_name: "Зарегистрировать изготовителя"
		}
	},
	{
		name: "personal_user_outgoing_request",
		path: "personal/outgoing-request",
		component: () => import("pages/personal/OutgoingRequest.vue"),
		meta: {
			route_name: "Новая заявка"
		}
	},
	{
		name: "personal_user_requests",
		path: "personal/user/requests",
		component: () => import("pages/personal/user/Requests.vue"),
		meta: {
			route_name: "Заявки"
		}
	},
]
