export default [
  {
    path: 'personal',
    component: () => import('pages/Personal'),
    meta: {
      routeName: 'Личный кабинет'
    },
  },
  {
    path: 'personal/orders',
    component: () => import('pages/Orders'),
    meta: {
      routeName: 'Список заказов'
    }
  },
  {
    path: 'personal/user',
    component: () => import('pages/User'),
    meta: {
      routeName: 'Персональные данные'
    }
  },
]
