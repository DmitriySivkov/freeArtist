export default [
	{
		name: "personal",
		path: "personal",
		component: () => import("pages/personal/Index"),
		meta: {
			route_name: "Личный кабинет",
			caption: "",
			icon: "account_circle",
			show_in_drawer: true,
			requires_auth: true
		},
	},
	{
		name: "personal_user",
		path: "personal/user",
		component: () => import("pages/personal/User"),
		meta: {
			route_name: "Персональные данные",
			requires_auth: true
		}
	}
]
