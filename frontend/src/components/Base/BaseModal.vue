<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div
      :class="modalSize"
      class="relative bg-white rounded-xl shadow-lg w-full mx-4 max-h-[90vh] overflow-y-auto"
    >
    <!-- Botão de fechar no canto superior direito -->
    <button
      @click="close"
      class="absolute top-2 right-2 text-gray-500 hover:text-red-600 transition"
      aria-label="Fechar"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

      <!-- Título -->
      <div class="border-b px-6 pt-4 pb-2">
        <h2 class="text-sm font-semibold text-gray-800 uppercase">
          {{ title }}
        </h2>
      </div>

      <!-- Conteúdo dinâmico -->
      <div class="px-6 py-4">
          <component v-if="component" :is="component" v-bind="props" @saved="onSave" @edited="onEdit"  />
      </div>

      <!-- Rodapé com botões 
      <div class="border-t px-6 py-4 flex justify-end gap-4">
        <button
          class="flex items-center gap-1 text-blue-600 hover:underline"
          @click="onSave"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          Save
        </button>

        <button
          class="flex items-center gap-1 text-blue-600 hover:underline"
          @click="close"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Cancel
        </button>
      </div>
      -->
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useModalStore } from '../../shared/store/modal'

const modal = useModalStore()

const isOpen = computed(() => modal.isOpen)
const component = computed(() => modal.component)
const props = computed(() => modal.props)
const size = computed(() => modal.size)
const title = computed(() => modal.title || 'Modal')

const modalSize = computed(() => {
  return {
    small: 'max-w-sm',
    medium: 'max-w-lg',
    large: 'max-w-3xl'
  }[size.value || 'medium']
})

const onSave = () => {
  if (typeof modal.onSave === 'function') {
    modal.onSave()
  }
}

const onEdit = () => {
  if (typeof modal.onEdit === 'function') {
    modal.onEdit()
  }
}

function close() {
  modal.close()
}
</script>
