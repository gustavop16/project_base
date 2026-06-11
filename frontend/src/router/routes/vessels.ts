import type { RouteRecordRaw } from 'vue-router'

const vesselRoutes: RouteRecordRaw[] = [
  {
    path: '/navios',
    name: 'vessel',
    component: () => import('../../views/vessel/index.vue'),
    meta: { requiresAuth: true, permission: 'vessel.view' },
  },
]

export default vesselRoutes
