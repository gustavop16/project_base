import type { RouteRecordRaw } from 'vue-router'

const clientRoutes: RouteRecordRaw[] = [
  {
    path: '/clientes',
    name: 'client',
    component: () => import('../../views/client/index.vue'),
    meta: { requiresAuth: true, permission: 'client.view' },
  },
  {
    path: '/clientes/:id',
    name: 'client-show',
    component: () => import('../../views/client/show.vue'),
    meta: { requiresAuth: true, permission: 'client.view' },
  },
]

export default clientRoutes
