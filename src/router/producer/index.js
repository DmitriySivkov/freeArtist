export default [
	{
		path: "/producers/:id",
		component: () => import("components/producers/Producer"),
		meta: {
			route_name: "Изготовитель"
		}
	},
]
