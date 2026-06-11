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
          <h2 class="text-3xl font-semibold text-center text-blue-900 mb-2">Criar conta</h2>
          <p class="text-center text-sm text-gray-500 mb-6">Preencha os dados abaixo para se cadastrar.</p>

          <form @submit.prevent="handleSubmit" class="space-y-3">

            <div class="flex items-center bg-blue-100 rounded px-4 py-2">
              <span class="text-blue-900 mr-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                </svg>
              </span>
              <input
                v-model="form.name"
                type="text"
                placeholder="Nome completo"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <div class="flex items-center bg-blue-100 rounded px-4 py-2">
              <span class="text-blue-900 mr-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 0v.217l8 5.333 8-5.333V6H4Zm16 1.783-8 5.334-8-5.334V18h16V7.783Z"/>
                </svg>
              </span>
              <input
                v-model="form.email"
                type="email"
                placeholder="E-mail"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <div class="flex items-center bg-blue-100 rounded px-4 py-2">
              <span class="text-blue-900 mr-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                </svg>
              </span>
              <input
                v-model="form.password"
                type="password"
                placeholder="Senha"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <div class="flex items-center bg-blue-100 rounded px-4 py-2">
              <span class="text-blue-900 mr-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                </svg>
              </span>
              <input
                v-model="form.password_confirmation"
                type="password"
                placeholder="Confirmar senha"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <p v-if="passwordMismatch" class="text-red-500 text-xs">As senhas não coincidem.</p>

            <p v-if="errorMessage" class="text-red-500 text-xs text-center">{{ errorMessage }}</p>

            <button
              type="submit"
              :disabled="loading || passwordMismatch"
              class="w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition disabled:opacity-50"
            >
              {{ loading ? 'Cadastrando...' : 'Criar conta' }}
            </button>
          </form>
        </div>

        <div v-else class="text-center space-y-4">
          <div class="flex justify-center">
            <svg class="w-16 h-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
          <h2 class="text-2xl font-semibold text-blue-900">Cadastro realizado!</h2>
          <p class="text-sm text-gray-500">Sua conta foi criada com sucesso.</p>
          <router-link to="/login" class="inline-block mt-4 w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition text-center">
            Fazer login
          </router-link>
        </div>

        <div v-if="!success" class="text-center">
          <router-link to="/login" class="text-sm text-blue-900 hover:underline">
            Já tem conta? Faça login
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { appConfig } from '../../config/app'
import authService from '../../shared/api/services/auth.service'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const loading      = ref(false)
const success      = ref(false)
const errorMessage = ref('')

const passwordMismatch = computed(() =>
  form.value.password_confirmation.length > 0 &&
  form.value.password !== form.value.password_confirmation
)

async function handleSubmit() {
  if (passwordMismatch.value) return
  errorMessage.value = ''

  try {
    loading.value = true
    await authService.register(
      form.value.name,
      form.value.email,
      form.value.password,
      form.value.password_confirmation
    )
    success.value = true
  } catch (err) {
    errorMessage.value = getErrorMessage(err?.response) || 'Erro ao criar conta. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-login {
  background: linear-gradient(90deg, rgba(21, 44, 65, 1) 0%, rgba(14, 59, 99, 1) 50%, rgba(1, 63, 119, 1) 100%);
}
</style>
