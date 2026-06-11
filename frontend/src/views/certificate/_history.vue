<template>
  <div class="p-2 space-y-1 min-h-[120px]">

    <div v-if="loading" class="text-center text-gray-400 text-sm py-8">
      Carregando histórico...
    </div>

    <div v-else-if="!events.length" class="text-center text-gray-400 text-sm py-8">
      Nenhum evento registrado.
    </div>

    <ol v-else class="relative border-l border-gray-200 ml-3">
      <li v-for="(event, i) in events" :key="i" class="mb-6 ml-6">

        <!-- Dot -->
        <span
          class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full ring-4 ring-white"
          :class="dotClass(event.type)"
        >
          <!-- requested -->
          <svg v-if="event.type === 'requested'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <!-- filled -->
          <svg v-else-if="event.type === 'filled'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <!-- approved -->
          <svg v-else-if="event.type === 'approved'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <!-- rejected -->
          <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </span>

        <!-- Content -->
        <div class="rounded border px-4 py-2.5 shadow-sm bg-white" :class="cardClass(event.type)">
          <div class="flex items-center justify-between gap-2 flex-wrap">
            <span class="text-sm font-semibold" :class="labelClass(event.type)">
              {{ eventLabel(event.type) }}
            </span>
            <time class="text-xs text-gray-400">{{ formatDate(event.date) }}</time>
          </div>
          <p v-if="event.user" class="text-xs text-gray-600 mt-0.5">
            {{ t('labels.responsible') }}: <span class="font-medium">{{ event.user.name }}</span>
          </p>
          <p v-if="event.notes" class="text-xs text-gray-500 mt-1 border-l-2 border-gray-200 pl-2 italic">
            {{ event.notes }}
          </p>
        </div>

      </li>
    </ol>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import certificateService from '../../shared/api/services/certificate.service'
import { useAlertStore }   from '../../shared/store/alert'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const props = defineProps({
  certificateId: { type: Number, required: true },
})

const { t }   = useI18n()
const alert   = useAlertStore()
const loading = ref(true)
const events  = ref([])

onMounted(async () => {
  try {
    const res = await certificateService.getHistory(props.certificateId)
    events.value = res.data.data
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loading.value = false
  }
})

const EVENT_LABELS = {
  requested: 'Solicitação',
  filled:    'Formulário preenchido',
  approved:  'Aprovado',
  rejected:  'Reprovado',
}

function eventLabel(type) {
  return EVENT_LABELS[type] ?? type
}

function dotClass(type) {
  return {
    requested: 'bg-blue-100 text-blue-600',
    filled:    'bg-purple-100 text-purple-600',
    approved:  'bg-green-100 text-green-600',
    rejected:  'bg-red-100 text-red-600',
  }[type] ?? 'bg-gray-100 text-gray-500'
}

function cardClass(type) {
  return {
    requested: 'border-blue-100',
    filled:    'border-purple-100',
    approved:  'border-green-100',
    rejected:  'border-red-100',
  }[type] ?? 'border-gray-100'
}

function labelClass(type) {
  return {
    requested: 'text-blue-700',
    filled:    'text-purple-700',
    approved:  'text-green-700',
    rejected:  'text-red-700',
  }[type] ?? 'text-gray-700'
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>
