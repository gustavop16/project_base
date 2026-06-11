import type { RouteRecordRaw } from 'vue-router'

const certificateFormRoutes: RouteRecordRaw[] = [
  {
    path: '/formularios',
    name: 'certificate-form',
    component: () => import('../../views/certificate_form/index.vue'),
    meta: { requiresAuth: true, permission: 'certificate_form.view' },
  },
 /* {
    path: '/formularios/:id/responder',
    name: 'certificate-form-fill',
    component: () => import('../../views/certificate_form/fill.vue'),
    meta: { requiresAuth: true, permission: 'form_response.create' },
  },
  */
]

export default certificateFormRoutes
