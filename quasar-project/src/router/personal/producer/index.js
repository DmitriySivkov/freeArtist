export default [
	{
		name: "personal_producer",
		path: "personal/producer",
		component: () => import("pages/personal/producer/Profiles.vue"),
		meta: {
			route_name: "Список профилей"
		}
	},
	{
		name: "personal_producer_orders",
		path: "personal/producer/orders",
		component: () => import("pages/personal/producer/Orders.vue"),
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
		name: "personal_producer_profile_detail",
		path: "personal/producer/:producer_id/profile",
		component: () => import("pages/personal/producer/ProfileDetail.vue"),
		meta: {
			route_name: "Профиль"
		}
	},
	{
		name: "personal_producer_products",
		path: "personal/producer/products",
		component: () => import("pages/personal/producer/Products.vue"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_products_detail",
		path: "personal/producer/:producer_id/products",
		component: () => import("pages/personal/producer/ProductsDetail.vue"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
]