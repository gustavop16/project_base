import { defineStore } from 'pinia'
import authService from "../api/services/auth.service.ts";
import { useRoute } from 'vue-router'
const route = useRoute()

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    user_id: Number(localStorage.getItem('user_id')) || null,
    user_photo: Number(localStorage.getItem('user_photo')) || null,
    token: localStorage.getItem('token') || null,
    permissions: JSON.parse(localStorage.getItem('permissions')) || [],
  }),

  actions: {
    async login({ email, password }) {
      const response = await authService.login(email, password)

      this.user        = response.data.user
      this.user_id     = response.data.user.id
      this.user_photo  = import.meta.env.VITE_APP_URL+'storage/profile/'+this.user_id+'/'+response.data.user.user_photo ?? 'images/no-photo.png';
      this.token       = response.data.access_token
      this.permissions = response.data.user.permissions ?? []

      // Persistência
      localStorage.setItem('user', JSON.stringify(this.user))
      localStorage.setItem('user_id', this.user_id)
      localStorage.setItem('user_photo', this.user_photo)
      localStorage.setItem('token', this.token)
      localStorage.setItem('permissions', JSON.stringify(this.permissions))
    },

    logout() {
      this.user        = null
      this.user_id     = null
      this.user_photo  = null
      this.token       = null
      this.permissions = []

      localStorage.removeItem('user')
      localStorage.removeItem('user_id')
      localStorage.removeItem('user_photo')
      localStorage.removeItem('token')
      localStorage.removeItem('permissions')
    }
  }
})
