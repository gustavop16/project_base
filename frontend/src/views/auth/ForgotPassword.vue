<template>
  <div class="flex h-screen">
    <!-- Lado Esquerdo -->
    <div class="w-1/2 bg-gradient-to-b from-blue-900 to-blue-800 text-white bg-login flex flex-col items-center justify-center px-10">
      <img src="/images/logo/logo-white.png" alt="Logo" class="w-24 h-24 mb-6" />
      <h1 class="text-4xl font-bold mb-2">PHOENIX CLEAN</h1>
      <p class="text-center text-sm">A 2UP software solution for aircrafts maintenance and operations</p>
    </div>

    <!-- Lado Direito -->
    <div class="w-1/2 flex items-center justify-center bg-white">
      <div class="w-full max-w-sm space-y-6">

        <div v-if="!sent">
          <h2 class="text-3xl font-semibold text-center text-blue-900 mb-2">Recuperar senha</h2>
          <p class="text-center text-sm text-gray-500 mb-6">
            Informe seu e-mail e enviaremos um link para redefinir sua senha.
          </p>

          <form @submit.prevent="handleSubmit">
            <div class="flex items-center bg-blue-100 rounded px-4 py-2 mb-4">
              <span class="text-blue-900 mr-2">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M2.038 5.61A2.01 2.01 0 0 0 2 6v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6c0-.12-.01-.238-.03-.352l-.866.65-7.89 5.917a2 2 0 0 1-2.429 0L2.884 6.378l-.846-.569Z"/>
                  <path d="M20.677 4.117A1.996 1.996 0 0 0 20 4H4c-.225 0-.44.037-.642.105l.758.509L12 10.97l7.885-6.349.792-.504Z"/>
                </svg>
              </span>
              <input
                v-model="email"
                type="email"
                placeholder="E-mail"
                required
                class="bg-transparent w-full focus:outline-none text-blue-900"
              />
            </div>

            <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition">
              Enviar link de recuperação
            </button>
          </form>
        </div>

        <div v-else class="text-center space-y-4">
          <div class="flex justify-center">
            <svg class="w-16 h-16 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
          </div>
          <h2 class="text-2xl font-semibold text-blue-900">Verifique seu e-mail</h2>
          <p class="text-sm text-gray-500">
            Enviamos um link de recuperação para <span class="font-medium text-blue-900">{{ email }}</span>.
            Verifique sua caixa de entrada.
          </p>
        </div>

        <div class="text-center">
          <router-link to="/login" class="text-sm text-blue-900 hover:underline">
            Voltar ao login
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore } from '../../shared/store/alert'
import authService from '../../shared/api/services/auth.service'

const loading = useLoadingStore()
const alert = useAlertStore()

const email = ref('')
const sent = ref(false)

const handleSubmit = async () => {
  try {
    loading.show()
    await authService.forgotPassword(email.value)
    sent.value = true
  } catch (error) {
    alert.show('Não foi possível enviar o e-mail. Verifique e tente novamente.', 'error')
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
