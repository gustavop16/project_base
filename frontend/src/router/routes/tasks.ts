import type { RouteRecordRaw } from 'vue-router'

const taskRoutes: RouteRecordRaw[] = [
  {
    path: '/tarefas',
    name: 'task',
    component: () => import('../../views/task/index.vue'),
    meta: { requiresAuth: true, permission: 'task.view' },
  },
  {
    path: '/tarefas/:id',
    name: 'task-show',
    component: () => import('../../views/task/tabs.vue'),
    meta: { requiresAuth: true, permission: 'task.view' },
  },
]

export default taskRoutes
