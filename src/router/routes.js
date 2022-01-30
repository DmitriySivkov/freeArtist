import personal from "src/router/personal"
import register from "src/router/register"
import drawer from "src/router/drawer"

const routes = [
	{
		path: "/",
		component: () => import("layouts/MainLayout.vue"),
		children: [
			...drawer,
			...personal,
			...register
		]
	},

	// Always leave this as last one,
	// but you can also remove it
	{
		path: "/:catchAll(.*)*",
		component: () => import("pages/Error404.vue")
	}
]

export default routes
