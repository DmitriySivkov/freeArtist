export default [
	{
		name: "producer_detail",
		path: "/producers/:id",
		component: () => import("pages/producer/ProducerStorefront"),
		meta: {
			route_name: "Витрина"
		}
	}
]
