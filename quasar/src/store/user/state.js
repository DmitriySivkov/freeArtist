export default {
	isLogged: false,
	data: {
		role: {},
		producer: {},
	},
	roles: {
		customer: 1,
		producer: 2
	},
	linkList: {
		auth: [
			{
				title: "Личный кабинет",
				caption: "",
				icon: "account_circle",
				link: "/personal"
			},
			{
				title: "Главная",
				caption: "",
				icon: "home",
				link: "/"
			},
			{
				title: "Выйти",
				caption: "Завершить сессию",
				icon: "logout",
				link: "/auth",
				dispatch: "user/logout",
				notification: {
					color: "green-4",
					textColor: "white",
					icon: "cloud_done",
					message: "Успешный логаут"
				}
			}
		],
		unauth: [
			{
				title: "Главная",
				caption: "",
				icon: "home",
				link: "/"
			},
			{
				title: "Авторизация",
				caption: "Войдите или зарегистрируйтесь",
				icon: "login",
				link: "/auth"
			},
		],
	}
}
