
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
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
      {
        path: 'register',
        component: () => import('pages/Register'),
        meta: {
          routeName: 'Регистрация'
        }
      },
      {
        path: 'personal',
        component: () => import('pages/Personal'),
        meta: {
          routeName: 'Личный кабинет'
        },
        children: [
          {
            path: 'orders',
            component: () => import('pages/Orders'),
            meta: {
              routeName: 'Список заказов'
            }
          },
          {
            path: 'user',
            component: () => import('pages/User'),
            meta: {
              routeName: 'Персональные данные'
            }
          },
        ]
      }
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
