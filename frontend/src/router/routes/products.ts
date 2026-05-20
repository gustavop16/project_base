import type { RouteRecordRaw } from 'vue-router'

const productRoutes: RouteRecordRaw[] = [
  {
    path: '/produtos',
    name: 'product',
    component: () => import('../../views/product/index.vue'),
    meta: { requiresAuth: true, permission: 'product.view' },
  },
  {
    path: '/produtos/:id',
    name: 'product-show',
    component: () => import('../../views/product/show.vue'),
    meta: { requiresAuth: true, permission: 'product.view' },
  },
]

export default productRoutes
