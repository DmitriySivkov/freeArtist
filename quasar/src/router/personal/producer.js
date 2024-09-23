import Orders from "src/pages/personal/producer/Orders.vue"
import RegisterProducer from "src/pages/personal/RegisterProducer.vue"
import ProducerProfile from "src/pages/personal/producer/ProducerProfile.vue"
import Products from "src/pages/personal/producer/Products.vue"
import ProductShow from "src/pages/personal/producer/ProductShow.vue"
import ProductCreate from "src/pages/personal/producer/ProductCreate.vue"
import PaymentMethods from "src/pages/personal/producer/PaymentMethods.vue"

export default [
	{
		name: "personal_producer_orders",
		path: "personal/producer/:team_id/orders",
		component: Orders,
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: RegisterProducer,
	},
	{
		name: "personal_producer_profile",
		path: "personal/producer/:team_id/profile",
		component: ProducerProfile,
	},
	{
		name: "personal_producer_products",
		path: "personal/producer/:team_id/products",
		component: Products,
	},
	{
		name: "personal_producer_products_show",
		path: "personal/producer/:producer_id/products/:product_id",
		component: ProductShow,
	},
	{
		name: "personal_producer_products_create",
		path: "personal/producer/:producer_id/products/create",
		component: ProductCreate,
	},
	{
		name: "personal_producer_payment_methods",
		path: "personal/producer/:team_id/payment-methods",
		component: PaymentMethods,
	}
]
