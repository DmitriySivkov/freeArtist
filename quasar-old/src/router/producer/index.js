export default [
	{
		name: "producer_detail",
		path: "/producers/:producer_id",
		component: () => import("pages/producer/ProducerStorefront"),
		meta: {
			route_name: "Витрина"
		}
	}
]
