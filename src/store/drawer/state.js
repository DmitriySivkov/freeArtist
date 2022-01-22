import { Notify } from 'quasar'

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
        apiCall: (store) => store.dispatch("user/logout").then(() => {
          Notify.create({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Успешный логаут'
          })
        })
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
