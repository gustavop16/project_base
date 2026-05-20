<template>
  <transition name="fade">
    <div
      v-if="dialog.isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg mx-4">
        <!-- Título -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ dialog.title }}
          </h3>
          <button
            class="text-gray-400 hover:text-red-500"
            @click="dialog.closeDialog"
          >
            ✕
          </button>
        </div>

        <!-- Conteúdo -->
        <div class="mb-6 text-gray-700">
          {{ dialog.content }}
        </div>

        <!-- Ações -->
        <div class="flex justify-end gap-2">
          <button
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded"
            @click="dialog.closeDialog"
          >
            Cancelar
          </button>
          <button
            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded"
            @click="dialog.confirm"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { useDialogStore } from '../../shared/store/dialog'
const dialog = useDialogStore()
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
