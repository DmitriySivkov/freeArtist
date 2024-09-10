import Profiles from "src/pages/personal/producer/Profiles.vue"
import Orders from "src/pages/personal/producer/Orders.vue"
import OrdersDetail from "src/pages/personal/producer/OrdersDetail.vue"
import RegisterProducer from "src/pages/personal/RegisterProducer.vue"
import ProducerSettings from "src/pages/personal/producer/ProducerSettings.vue"
import Products from "src/pages/personal/producer/Products.vue"
import ProductsDetail from "src/pages/personal/producer/ProductsDetail.vue"
import ProductShow from "src/pages/personal/producer/ProductShow.vue"
import ProductCreate from "src/pages/personal/producer/ProductCreate.vue"
import PaymentMethods from "src/pages/personal/producer/PaymentMethods.vue"
import PaymentMethodsDetail from "src/pages/personal/producer/PaymentMethodsDetail.vue"

export default [
	{
		name: "personal_producer",
		path: "personal/producer",
		component: Profiles,
		meta: {
			route_name: "Список профилей"
		}
	},
	{
		name: "personal_producer_orders",
		path: "personal/producer/orders",
		component: Orders,
		meta: {
			route_name: "Список заказов"
		}
	},
	{
		name: "personal_producer_orders_detail",
		path: "personal/producer/:producer_id/orders",
		component: OrdersDetail,
		meta: {
			route_name: "Список заказов"
		}
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: RegisterProducer,
		meta: {
			route_name: "Зарегистрировать изготовителя"
		}
	},
	{
		name: "personal_producer_settings",
		path: "personal/producer/:producer_id/settings",
		component: ProducerSettings,
		meta: {
			route_name: "Настройки"
		}
	},
	{
		name: "personal_producer_products",
		path: "personal/producer/products",
		component: Products,
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_products_detail",
		path: "personal/producer/:producer_id/products",
		component: ProductsDetail,
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_products_detail_show",
		path: "personal/producer/:producer_id/products/:product_id",
		component: ProductShow,
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_products_detail_create",
		path: "personal/producer/:producer_id/products/create",
		component: ProductCreate,
		meta: {
			route_name: "Настройки продуктов"
		}
	},
	{
		name: "personal_producer_payment_methods",
		path: "personal/producer/payment-methods",
		component: PaymentMethods,
		meta: {
			route_name: "Способы оплаты"
		}
	},
	{
		name: "personal_producer_payment_methods_detail",
		path: "personal/producer/:producer_id/payment-methods",
		component: PaymentMethodsDetail,
		meta: {
			route_name: "Способы оплаты"
		}
	},

]
