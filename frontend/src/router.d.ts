import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    requiresGuest?: boolean
    permission?: string | null
    module?: string
    title?: string
    icon?: string
  }
}
