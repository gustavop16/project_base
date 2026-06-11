<template>
  <div class="flex h-screen">
    <!-- Lado Esquerdo -->
    <div class="w-1/2 bg-gradient-to-b from-blue-900 to-blue-800 text-white bg-login flex flex-col items-center justify-center px-10">
      <img :src="appConfig.logo.auth" alt="Logo" class="mb-6" />
      <h1 class="text-4xl font-bold mb-2">{{ appConfig.name }}</h1>
      <p class="text-center text-sm">{{ appConfig.description }}</p>
    </div>

    <!-- Lado Direito -->
    <div class="w-1/2 flex items-center justify-center bg-white">
      <div class="w-full max-w-sm space-y-6">

        <div v-if="!success">
          <h2 class="text-3xl font-semibold text-center text-blue-900 mb-2">Nova senha</h2>
          <p class="text-center text-sm text-gray-500 mb-6">
            Escolha uma nova senha para sua conta.
          </p>

          <form @submit.prevent="handleSubmit">
            <div class="flex items-center bg-blue-100 rounded px-4 py-2 mb-2">
              <span class="text-blue-900 mr-2">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                </svg>
              </span>
              <input
                v-model="form.password"
                type="password"
                placeholder="Nova senha"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <div class="flex items-center bg-blue-100 rounded px-4 py-2 mb-1">
              <span class="text-blue-900 mr-2">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                </svg>
              </span>
              <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirmar nova senha"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <p v-if="passwordMismatch" class="text-red-500 text-xs mb-3">
              As senhas não coincidem.
            </p>
            <div v-else class="mb-3"></div>

            <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition">
              Redefinir senha
            </button>
          </form>
        </div>

        <div v-else class="text-center space-y-4">
          <div class="flex justify-center">
            <svg class="w-16 h-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
          <h2 class="text-2xl font-semibold text-blue-900">Senha redefinida!</h2>
          <p class="text-sm text-gray-500">Sua senha foi alterada com sucesso.</p>
          <router-link to="/login" class="inline-block mt-4 w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition text-center">
            Ir para o login
          </router-link>
        </div>

        <div v-if="!success" class="text-center">
          <router-link to="/login" class="text-sm text-blue-900 hover:underline">
            Voltar ao login
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { appConfig } from '../../config/app'
import { useRoute } from 'vue-router'
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore } from '../../shared/store/alert'
import authService from '../../shared/api/services/auth.service'

const route = useRoute()
const loading = useLoadingStore()
const alert = useAlertStore()

const form = ref({
  password: '',
  password_confirmation: '',
})

const success = ref(false)

const passwordMismatch = computed(() =>
  form.value.password_confirmation.length > 0 &&
  form.value.password !== form.value.password_confirmation
)

const handleSubmit = async () => {
  if (passwordMismatch.value) return

  try {
    loading.show()
    const token = route.query.token
    const email = route.query.email
    await authService.resetPassword(token, email, form.value.password, form.value.password_confirmation)
    success.value = true
  } catch (error) {
    alert.show('Não foi possível redefinir a senha. O link pode ter expirado.', 'error')
    console.error(error)
  } finally {
    loading.hide()
  }
}
</script>

<style scoped>
.bg-login {
  background: linear-gradient(90deg, rgba(21, 44, 65, 1) 0%, rgba(14, 59, 99, 1) 50%, rgba(1, 63, 119, 1) 100%);
}
</style>
