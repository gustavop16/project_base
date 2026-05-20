import type { RouteRecordRaw } from 'vue-router'

const placeRoutes: RouteRecordRaw[] = [
  {
    path: '/locais',
    name: 'place',
    component: () => import('../../views/place/index.vue'),
    meta: { requiresAuth: true, permission: 'place.view' },
  },
  {
    path: '/locais/:id',
    name: 'place-show',
    component: () => import('../../views/place/show.vue'),
    meta: { requiresAuth: true, permission: 'place.view' },
  },
]

export default placeRoutes
