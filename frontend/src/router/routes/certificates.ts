import type { RouteRecordRaw } from 'vue-router'

const certificateRoutes: RouteRecordRaw[] = [
  {
    path: '/certificados',
    name: 'certificates',
    component: () => import('../../views/certificate/index.vue'),
    meta: { requiresAuth: true, permission: 'certificates.view' },
  },
  {
    path: '/certificados/:id/responder',
    name: 'certificates-respond',
    component: () => import('../../views/certificate/respond.vue'),
    meta: { requiresAuth: true, permission: 'form_response.create' },
  },
  {
    path: '/certificados/:id/respostas',
    name: 'certificates-response',
    component: () => import('../../views/certificate/response.vue'),
    meta: { requiresAuth: true, permission: 'certificates.view' },
  },
]

export const certificatePublicRoutes: RouteRecordRaw[] = [
  {
    path: '/certificados/verificar/:token',
    name: 'certificates-verify',
    component: () => import('../../views/certificate/verify.vue'),
    meta: { requiresAuth: false },
  },
]

export default certificateRoutes
