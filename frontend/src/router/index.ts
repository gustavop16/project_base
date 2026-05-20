import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../shared/store/auth.js'
import { usePermissions } from '../shared/composables/usePermissions'
import About from '../views/About.vue'
import Index from '../layout/index.vue'
import Home from '../views/Home.vue'

import authRoutes from './routes/auth'
import userRoutes from './routes/users'
import clientRoutes from './routes/clients'
import placeRoutes from './routes/places'
import productRoutes from './routes/products'
import taskRoutes from './routes/tasks'
import planningRoutes from './routes/plannings'

const routes: RouteRecordRaw[] = [
  {
    path: '/index',
    name: 'Index',
    component: Index,
    meta: { title: 'dashboard', requiresAuth: true },
    children: [
      {
        path: '/about',
        name: 'About',
        component: About,
        meta: { requiresAuth: true },
      },
      {
        path: '/home',
        name: 'Home',
        component: Home,
        meta: { requiresAuth: true },
      },
      ...userRoutes,
      ...clientRoutes,
      ...placeRoutes,
      ...productRoutes,
      ...taskRoutes,
      ...planningRoutes,
    ],
  },
  ...authRoutes,
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from, next) => {
  const auth = useAuthStore()

  if (auth.token && to.meta.requiresGuest) {
    return next({ name: 'Home' })
  }

  if (to.meta.requiresAuth && (!auth.token || auth.token === 'undefined')) {
    return next('/login')
  }

  if (to.meta.permission) {
    const { can } = usePermissions()
    if (!can(to.meta.permission)) return next({ name: 'Home' })
  }

  next()
})

export default router
