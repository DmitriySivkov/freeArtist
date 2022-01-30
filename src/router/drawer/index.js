export default [
  {
    path: '',
    component: () => import('pages/Index'),
    meta: {
      routeName: 'Главная'
    }
  },
  {
    path: 'auth',
    component: () => import('pages/Auth'),
    meta: {
      routeName: 'Войти'
    }
  },
]
