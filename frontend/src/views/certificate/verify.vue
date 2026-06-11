<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-md p-8">

      <!-- Carregando -->
      <div v-if="loading" class="text-center text-gray-500 py-10">
        Verificando certificado...
      </div>

      <!-- Não encontrado / não aprovado -->
      <div v-else-if="error" class="text-center">
        <div class="flex justify-center mb-4">
          <div class="bg-red-100 rounded-full p-4">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
        </div>
        <h2 class="text-xl font-bold text-red-600 mb-2">Certificado não encontrado</h2>
        <p class="text-gray-500 text-sm">Este certificado não existe ou ainda não foi aprovado.</p>
      </div>

      <!-- Certificado ainda não iniciado -->
      <div v-else-if="validity === 'not_started'" class="text-center">
        <div class="flex justify-center mb-4">
          <div class="bg-yellow-100 rounded-full p-4">
            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <h2 class="text-xl font-bold text-yellow-700 mb-2">{{ t('labels.certificate_not_started') }}</h2>
        <p class="text-gray-600 text-sm">
          {{ t('labels.certificate_valid_from') }}
          <span class="font-semibold">{{ formatDate(certificate.monitoring_start) }}</span>
        </p>
        <div class="mt-6 divide-y divide-gray-100">
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">Formulário</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.form_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.vessel') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.vessel_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_start') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_start) }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_end') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_end) }}</span>
          </div>
        </div>
      </div>

      <!-- Certificado expirado -->
      <div v-else-if="validity === 'expired'" class="text-center">
        <div class="flex justify-center mb-4">
          <div class="bg-red-100 rounded-full p-4">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <h2 class="text-xl font-bold text-red-600 mb-2">{{ t('labels.certificate_expired') }}</h2>
        <p class="text-gray-500 text-sm">
          {{ t('labels.certificate_expired_on') }}
          <span class="font-semibold">{{ formatDate(certificate.monitoring_end) }}</span>
        </p>
        <div class="mt-6 divide-y divide-gray-100">
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">Formulário</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.form_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.vessel') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.vessel_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_start') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_start) }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_end') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_end) }}</span>
          </div>
        </div>
      </div>

      <!-- Certificado válido -->
      <div v-else-if="validity === 'valid'">
        <div class="flex justify-center mb-6">
          <div class="bg-green-100 rounded-full p-4">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>

        <h2 class="text-2xl font-bold text-center text-[#1e3a5f] mb-1">{{ appConfig.name }}</h2>
        <p class="text-center text-green-600 font-semibold mb-6">{{ t('labels.certificate_valid') }}</p>

        <div class="divide-y divide-gray-100">
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">Formulário</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.form_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.vessel') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.vessel_name }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_start') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_start) }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.monitoring_end') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDate(certificate.monitoring_end) }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.approved_by') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ certificate.approved_by ?? '—' }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">{{ t('labels.approved_at') }}</span>
            <span class="text-sm font-medium text-gray-800">{{ formatDateTime(certificate.approved_at) }}</span>
          </div>
          <div class="py-3 flex justify-between">
            <span class="text-sm text-gray-500">Certificado Nº</span>
            <span class="text-sm font-medium text-gray-800">{{ String(certificate.id).padStart(6, '0') }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { appConfig } from '../../config/app'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import certificateService from '../../shared/api/services/certificate.service'

const route = useRoute()
const { t } = useI18n()

const loading     = ref(true)
const error       = ref(false)
const certificate = ref(null)

const validity = computed(() => {
  if (!certificate.value) return null
  const today = new Date().toISOString().split('T')[0]
  const start = certificate.value.monitoring_start
  const end   = certificate.value.monitoring_end

  if (start && today < start) return 'not_started'
  if (end   && today > end)   return 'expired'
  return 'valid'
})

onMounted(async () => {
  try {
    const response = await certificateService.verify(route.params.token)
    certificate.value = response.data.data
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
})

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const [y, m, d] = dateStr.split('-')
  return `${d}/${m}/${y}`
}

function formatDateTime(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>
