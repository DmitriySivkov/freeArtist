export default {
	data: {},
	linkList: {
		auth: [
			{
				title: "Личный кабинет",
				caption: "",
				icon: "account_circle",
				link: "/personal"
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
				title: "Авторизация",
				caption: "Войдите или зарегистрируйтесь",
				icon: "login",
				link: "/auth"
			},
		],
	}
}
