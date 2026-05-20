import { useAuthStore } from '../store/auth'

export function usePermissions() {
  const auth = useAuthStore()

  function can(permission: string | null | undefined): boolean {

    console.log('verifica permi')
    //console.log(permission)
    console.log(auth.permissions)

    if (!permission) return true
    return (auth.permissions as string[]).includes(permission)
  }

  return { can }
}
