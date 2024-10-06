import Orders from "src/pages/personal/producer/Orders.vue"
import RegisterProducer from "src/pages/personal/RegisterProducer.vue"
import ProducerProfile from "src/pages/personal/producer/ProducerProfile.vue"
import PaymentMethods from "src/pages/personal/producer/PaymentMethods.vue"
import ProducerRequests from "src/pages/personal/producer/ProducerRequests.vue"
import Products from "src/pages/personal/producer/Products.vue"

export default [
	{
		name: "personal_producer_orders",
		path: "personal/producer/:producer_id/orders",
		component: Orders,
	},
	{
		name: "personal_register_producer",
		path: "personal/register-producer",
		component: RegisterProducer,
	},
	{
		name: "personal_producer_profile",
		path: "personal/producer/:producer_id/profile",
		component: ProducerProfile,
	},
	{
		name: "personal_producer_payment_methods",
		path: "personal/producer/:producer_id/payment-methods",
		component: PaymentMethods,
	},
	{
		name: "personal_producer_requests",
		path: "personal/producer/:producer_id/requests",
		component: ProducerRequests,
	},
	{
		name: "personal_producer_products",
		path: "/personal/producer/:producer_id/products",
		component: Products,
	},
]
