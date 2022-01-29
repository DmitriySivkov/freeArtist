import {api} from "boot/axios";

export default {
  data: {},
  linkList: {
    auth: [
      {
        title: 'Личный кабинет',
        caption: '',
        icon: 'account_circle',
        link: '/personal'
      },
      {
        title: 'Выйти',
        caption: 'Завершить сессию',
        icon: 'logout',
        link: 'logout',
        apiCall: (store) => new Promise((resolve, reject) => {
          store.dispatch("user/logout")
            .then(() => resolve())
            .catch((error) => reject(error.response.data.errors))
        })
      }
    ],
    unauth: [
      {
        title: 'Авторизация',
        caption: 'Войдите или зарегистрируйтесь',
        icon: 'login',
        link: '/auth'
      },
    ],
  }
}
