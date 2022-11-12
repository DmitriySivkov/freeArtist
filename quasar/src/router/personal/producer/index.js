export default [
	{
		name: "personal_producer_orders",
		path: "personal/producer/orders",
		component: () => import("pages/personal/producer/Orders"),
		meta: {
			route_name: "Список заказов"
		}
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: () => import("pages/personal/RegisterProducer"),
		meta: {
			route_name: "Зарегистрировать изготовителя"
		}
	},
	{
		name: "personal_coworking_request",
		path: "personal/coworking-request",
		component: () => import("pages/personal/CoworkingRequest"),
		meta: {
			route_name: "Заявка: присоединиться к изготовителю"
		}
	},
	{
		name: "personal_producer_requests",
		path: "personal/producer/requests",
		component: () => import("pages/personal/producer/Requests"),
		meta: {
			route_name: "Заявки"
		}
	},
	{
		name: "personal_producer_team_requests",
		path: "personal/producer/:team_id/requests",
		component: () => import("pages/personal/producer/RequestsDetail"),
		meta: {
			route_name: "Заявки"
		}
	},
	{
		name: "personal_producer_permissions",
		path: "personal/producer/permissions",
		component: () => import("pages/personal/producer/Permissions"),
		meta: {
			route_name: "Разрешения"
		}
	},
	{
		name: "personal_producer_team_permissions",
		path: "personal/producer/:team_id/permissions",
		component: () => import("pages/personal/producer/PermissionsDetail"),
		meta: {
			route_name: "Разрешения"
		}
	},
	{
		name: "personal_producer_products",
		path: "personal/producer/products",
		component: () => import("pages/personal/producer/Products"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_team_products",
		path: "personal/producer/:team_id/products",
		component: () => import("pages/personal/producer/ProductsDetail"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
]
