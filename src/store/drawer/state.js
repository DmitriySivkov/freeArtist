export default {
  data: {},
  linkList: {
    auth: [
      {
        title: 'Выйти',
        caption: 'Завершить сессию',
        icon: 'account_circle',
        link: 'logout',
        isApiCall: true,
        apiCall: (store) => store.dispatch("user/logout")
      }
    ],
    unauth: [
      {
        title: 'Авторизация',
        caption: 'Войдите или зарегистрируйтесь',
        icon: 'account_circle',
        link: 'auth'
      }
    ],
  }
}
