export default [
	{
		name: "producer_products",
		path: "/producers/:producer_id/products",
		component: () => import("pages/producer/ProducerProducts.vue"),
		meta: {
			route_name: "Витрина"
		}
	},
	{
		name: "producer_products_detail",
		path: "/producers/:producer_id/products/:product_id",
		component: () => import("pages/producer/ProducerProductsDetail.vue"),
		meta: {
			route_name: "Витрина"
		}
	}
]
