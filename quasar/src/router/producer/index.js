export default [
	{
		path: "/producers/:id",
		component: () => import("components/producers/ProducerShowcase"),
		meta: {
			route_name: "Витрина"
		}
	},
]
