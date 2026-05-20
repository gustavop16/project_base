import type { RouteRecordRaw } from 'vue-router'

const planningRoutes: RouteRecordRaw[] = [
  {
    path: '/planejamentos',
    name: 'planning',
    component: () => import('../../views/planning/index.vue'),
    meta: { requiresAuth: true, permission: 'planning.view' },
  },
  {
    path: '/planejamentos/:id',
    name: 'planning-show',
    component: () => import('../../views/planning/tabs.vue'),
    meta: { requiresAuth: true, permission: 'planning.view' },
  },
]

export default planningRoutes
