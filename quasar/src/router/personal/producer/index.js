export default [
	{
		name: "personal_producer_orders",
		path: "personal/producer/orders",
		component: () => import("pages/personal/producer/Orders"),
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
	{
		name: "personal_producer_team_requests",
		path: "personal/producer/:id/requests",
		component: () => import("pages/personal/producer/RequestsDetail"),
		meta: {
			route_name: "Заявки",
			requires_auth: true
		}
	},
	{
		name: "personal_producer_permissions",
		path: "personal/producer/permissions",
		component: () => import("pages/personal/producer/Permissions"),
		meta: {
			route_name: "Разрешения",
			requires_auth: true
		}
	},
	{
		name: "personal_producer_team_permissions",
		path: "personal/producer/:id/permissions",
		component: () => import("pages/personal/producer/PermissionsDetail"),
		meta: {
			route_name: "Разрешения",
			requires_auth: true
		}
	},
	{
		name: "personal_producer_storefront_settings",
		path: "personal/producer/storefront/settings",
		component: () => import("pages/personal/producer/StorefrontSettings"),
		meta: {
			route_name: "Витрина: список настроек",
			requires_auth: true
		}
	},
	{
		name: "personal_producer_storefront_settings_products",
		path: "personal/producer/storefront/settings/products",
		component: () => import("pages/personal/producer/StorefrontSettingsProducts"),
		meta: {
			route_name: "Витрина: настройки продуктов",
			requires_auth: true
		}
	},
]
