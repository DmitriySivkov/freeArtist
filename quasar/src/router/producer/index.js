export default [
	{
		name: "producer_products",
		path: "/producers/:producer_id/products",
		component: () => import("pages/producer/ProducerProducts.vue"),
		meta: {
			route_name: "Витрина"
		}
	}
]
