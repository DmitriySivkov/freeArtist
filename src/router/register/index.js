export default [
  {
    path: 'register',
    component: () => import('pages/Register'),
    meta: {
      routeName: 'Регистрация'
    },
  },
  {
    path: 'register/user',
    component: () => import('components/register/UserRegister'),
    meta: {
      routeName: 'Регистрация: пользователь',
      roleId: 1
    }
  },
  {
    path: 'register/producer',
    component: () => import('components/register/ProducerRegister'),
    meta: {
      routeName: 'Регистрация: мастер',
      roleId: 2
    }
  },
]
