export default [
	{
		name: "home",
		path: "",
		component: () => import("pages/Producers"),
		meta: {
			route_name: "Главная",
			caption: "",
			icon: "home",
			show_in_drawer: true,
		}
	}
]
