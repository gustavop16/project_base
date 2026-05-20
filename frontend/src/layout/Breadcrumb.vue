<script setup>
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const router = useRouter()



const { t } = useI18n()

defineProps({
  title: {
    type: String,
    default: 'Título'
  },
  linkText: {
    type: String,
    default: ''
  },
  linkHref: {
    type: String,
    default: '#'
  },
  showFilter: {
    type: Boolean,
    default: false
  },
  showExport: {
    type: Boolean,
    default: false
  },
  showAdd: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['filter', 'export', 'add'])

function backPage() {
  router.back() // ou router.go(-1)
}
</script>

<template>
  <div class="flex items-center justify-between border-b pt-6 pb-6 sticky top-0 bg-white z-10 shadow-sm">
    <!-- Título + Link -->
    <div class="">
      <h2 class="text-gray-800 font-medium text-lg">{{ title }}</h2>
      <a v-if="linkText" href="#" @click="backPage" class="text-blue-600 text-sm hover:underline">
        Voltar
      </a>
      <span v-if="linkText" class="text-blue-400 text-sm">
        - {{ linkText }}
      </span>
    </div>

    <!-- Botões -->
    <div class="flex gap-2">
      <button v-if="showFilter"
        class="flex items-center gap-1 text-gray-700 text-sm border rounded px-3 py-1.5 hover:bg-gray-100"
        @click="$emit('filter')"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2H3V4zm0 4h14v2H3V8zm0 4h9v2H3v-2z" />
        </svg>
        Filters
      </button>

      <button v-if="showExport"
        class="flex items-center gap-1 text-gray-700 text-sm border rounded px-3 py-1.5 hover:bg-gray-100"
        @click="$emit('export')"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v2H3V3zm0 4h14v2H3V7zm0 4h9v2H3v-2zm0 4h9v2H3v-2z" />
        </svg>
        Export
      </button>

      <button v-if="showAdd"
        class="flex items-center gap-1 text-gray-700 text-sm border rounded px-3 py-1.5 hover:bg-gray-100"
        @click="$emit('add')"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ t("labels.add") }}
      </button>

      <!--
      <button class="border text-sm rounded px-3 py-1.5 flex items-center gap-1 text-gray-700 hover:bg-gray-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        {{ t("labels.return") }}
      </button>
      -->

    </div>
  </div>
</template>
