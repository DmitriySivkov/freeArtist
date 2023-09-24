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
	{
		name: "personal_producer_products_detail_show",
		path: "personal/producer/:producer_id/products/:product_id",
		component: () => import("pages/personal/producer/ProductShow.vue"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_products_detail_create",
		path: "personal/producer/:producer_id/products/create",
		component: () => import("pages/personal/producer/ProductCreate.vue"),
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_storefront",
		path: "personal/producer/storefront",
		component: () => import("pages/personal/producer/Storefront.vue"),
		meta: {
			route_name: "Настройки витрины"
		}
	},
	{
		name: "personal_producer_storefront_detail",
		path: "personal/producer/:producer_id/storefront",
		component: () => import("pages/personal/producer/StorefrontDetail.vue"),
		meta: {
			route_name: "Настройки витрины"
		}
	},

	{
		name: "personal_producer_payment_methods",
		path: "personal/producer/payment-methods",
		component: () => import("pages/personal/producer/PaymentMethods.vue"),
		meta: {
			route_name: "Способы оплаты"
		}
	},
	{
		name: "personal_producer_payment_methods_detail",
		path: "personal/producer/:producer_id/payment-methods",
		component: () => import("pages/personal/producer/PaymentMethodsDetail.vue"),
		meta: {
			route_name: "Способы оплаты"
		}
	},

]
