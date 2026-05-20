<template>
  <header class="fixed top-0 left-0 w-full h-16 bg-primary shadow-md z-50 flex items-center justify-between px-4">
    <!-- Logo -->
    <div class="text-xl font-bold text-gray-800">
      <img src="/images/logo/logo-pca.png" class="h-px-5" alt="" height="40" width="40">
    </div>

    <!-- User profile -->
    <div class="relative">
      <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">
        <img
          class="w-10 h-10 rounded-full object-cover border-2 border-gray-300"
          :src="user_photo"
          :alt="user_name"
        />
      </button>

      <!-- Dropdown -->
      <transition name="fade">
        <div
          v-if="dropdownOpen"
          class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-50"
        >
          <ul class="py-1 text-sm text-gray-700">
            <li class="px-4 py-2 font-semibold text-gray-800 select-none text-center">{{ user_name }}</li>
            <li @click="getById" class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-t flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
              Meus dados
            </li>
            <li @click="openChangePassword" class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-t flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
              </svg>
              Alterar senha
            </li>
            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-t flex items-center gap-2" @click="handleLogout">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
              </svg>
              Sair
            </li>
          </ul>
        </div>
      </transition>
    </div>
  </header>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useAuthStore } from './../shared/store/auth'
import { useRouter } from 'vue-router'
import { useLoadingStore } from "./../shared/store/loading";
import { useModalStore } from './../shared/store/modal'
import userService from "./../shared/api/services/user.service";
import form from './../views/users/staff/_form.vue'
import passwordForm from './../views/users/account/password.vue'

const { t } = useI18n();
const modal = useModalStore();
const router = useRouter()
const auth = useAuthStore()
const loading = useLoadingStore();

const dropdownOpen = ref(false)
const user_name = ref('');
const user_photo = ref('');
const dataUser = ref([]);

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

 async function handleLogout() {
  try {
    loading.show();
    await auth.logout();
    router.push('/login')
  } catch (error) {
    alert('Erro ao tentar fazer logout')
    console.error(error)
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
  

 
}

// Fecha o dropdown se clicar fora
const handleClickOutside = (event) => {
  if (!event.target.closest('header')) {
    dropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  user_name.value  = auth.user.name
  user_photo.value = localStorage.getItem('user_photo')
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

async function getById() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await userService.getById(auth.user_id);
    dataUser.value = response.data.data;
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
    editUser();
  }
}

function editUser() {
  modal.open(
    form, 
    {formUpdate : dataUser }, 
    'large',
    t('labels.edit')+' '+t('labels.user'),
    refreshUser
  )
}

function refreshUser(){
  location.reload();
}

function openChangePassword() {
  dropdownOpen.value = false
  modal.open(
    passwordForm,
    {},
    'small',
    'Alterar senha',
    null
  )
}

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
