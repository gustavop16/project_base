import type { RouteRecordRaw } from 'vue-router'
import Login from '../../views/auth/Login.vue'
import ForgotPassword from '../../views/auth/ForgotPassword.vue'
import ResetPassword from '../../views/auth/ResetPassword.vue'
import Register from '../../views/auth/Register.vue'

const authRoutes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'pageLogin',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPassword,
    meta: { requiresGuest: true }
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPassword,
    meta: { requiresGuest: true }
  },
]

export default authRoutes
