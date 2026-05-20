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
        <h2 class="text-3xl font-semibold text-center text-blue-900">Login</h2>
        <form @submit.prevent="handleLogin">
        <div>
          <div class="flex items-center bg-blue-100 rounded px-4 py-2 mb-2">
            <span class="text-blue-900 mr-2">
              <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
              </svg>
            </span>
            <input v-model="email" type="text" placeholder="User" class="bg-transparent w-full focus:outline-none text-blue-900" />
          </div>
        </div>

        <div>
          <div class="flex items-center bg-blue-100 rounded px-4 py-2 mb-2">
            <span class="text-blue-900 mr-2">
              <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
              </svg>
            </span>
            <input v-model="password" type="password" placeholder="Password" class="bg-transparent w-full focus:outline-none text-blue-900" />
          </div>
        </div>

        <div class="text-right text-sm mb-2">
          <router-link to="/forgot-password" class="text-blue-900 hover:underline">Esqueci minha senha</router-link>
        </div>

        <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-full hover:bg-blue-800 transition">
          Login
        </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../shared/store/auth'
import { useLoadingStore } from "../../shared/store/loading";
import { useAlertStore } from "../../shared/store/alert";

const router = useRouter()
const auth = useAuthStore()
const loading = useLoadingStore();
const alert = useAlertStore();

//const email = ref('gustavo.cma@gmail.com')
const email = ref('')
const password = ref('')

const handleLogin = async () => {
  try {
    loading.show();
    await auth.login({ email: email.value, password: password.value })
    router.push('/home')
  } catch (error) {
    alert.show("Credenciais incorretas", "error");
    console.error(error)
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}
</script>



<style scoped>
.bg-login{
  background: linear-gradient(90deg, rgba(21, 44, 65, 1) 0%, rgba(14, 59, 99, 1) 50%, rgba(1, 63, 119, 1) 100%);
} 
</style>
