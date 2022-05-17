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
	},
	{
		name: "login",
		path: "auth",
		component: () => import("pages/Auth"),
		meta: {
			route_name: "Войти",
			caption: "Получить доступ к личному кабинету",
			icon: "login",
			show_in_drawer: true,
		}
	},
	{
		name: "logout",
		path: "auth",
		component: () => import("pages/Auth"),
		meta: {
			route_name: "Выйти",
			caption: "Завершить сессию",
			icon: "logout",
			show_in_drawer: true,
			requires_auth: true,
			dispatch: "user/logout",
			redirect: "login",
			notification: {
				color: "green-4",
				textColor: "white",
				icon: "cloud_done",
				message: "Успешный логаут"
			}
		}
	}
]
