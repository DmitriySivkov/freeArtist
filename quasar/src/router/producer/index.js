export default [
	{
		name: "producer_detail",
		path: "/producers/:id",
		component: () => import("components/producers/ProducerShowcase"),
		meta: {
			route_name: "Витрина"
		}
	},
]
