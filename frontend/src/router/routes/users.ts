import type { RouteRecordRaw } from 'vue-router'

const userRoutes: RouteRecordRaw[] = [
  {
    path: '/usuarios/usuarios',
    name: 'user-staff',
    component: () => import('../../views/users/staff/index.vue'),
    meta: { requiresAuth: true, permission: 'users.view' },
  },
  {
    path: '/usuarios/usuario/:id',
    name: 'user-staff-show',
    component: () => import('../../views/users/staff/show.vue'),
    meta: { requiresAuth: true, permission: 'users.view' },
  },
  {
    path: '/usuarios/minha-conta',
    name: 'user-my-account',
    component: () => import('../../views/users/account/index.vue'),
    meta: { requiresAuth: true },
  },
]

export default userRoutes
