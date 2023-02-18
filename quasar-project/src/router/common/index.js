export default [
	{
		name: "home",
		path: "",
		component: () => import("pages/Home.vue"),
		meta: {
			route_name: "Главная",
		}
	}
]
