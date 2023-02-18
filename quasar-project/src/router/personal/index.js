import userRoutes from "src/router/personal/user"
import producerRoutes from "src/router/personal/producer"
import teamRoutes from "src/router/personal/team"

export default [
	...userRoutes,
	...producerRoutes,
	...teamRoutes,
	{
		name: "personal",
		path: "personal",
		component: () => import("pages/personal/Index.vue"),
		meta: {
			route_name: "Личный кабинет",
		},
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: () => import("pages/personal/User.vue"),
		meta: {
			route_name: "Профиль"
		}
	}
]
