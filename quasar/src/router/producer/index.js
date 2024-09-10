import ProducerProducts from "src/pages/producer/ProducerProducts.vue"
import ProducerProductsDetail from "src/pages/producer/ProducerProductsDetail.vue"

export default [
	{
		name: "producer_products",
		path: "/producers/:producer_id/products",
		component: ProducerProducts,
		meta: {
			route_name: "Витрина"
		}
	},
	{
		name: "producer_products_detail",
		path: "/producers/:producer_id/products/:product_id",
		component: ProducerProductsDetail,
		meta: {
			route_name: "Витрина"
		}
	}
]
