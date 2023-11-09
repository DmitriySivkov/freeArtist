export default [
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: () => import("pages/personal/RegisterProducer.vue"),
		meta: {
			route_name: "Зарегистрировать изготовителя"
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
