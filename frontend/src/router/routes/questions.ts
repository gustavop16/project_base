import type { RouteRecordRaw } from 'vue-router'

const questionRoutes: RouteRecordRaw[] = [
  {
    path: '/perguntas',
    name: 'question',
    component: () => import('../../views/question/index.vue'),
    meta: { requiresAuth: true, permission: 'question.view' },
  },
]

export default questionRoutes
