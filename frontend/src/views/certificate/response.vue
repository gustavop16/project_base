<template>
  <div>
    <breadcrumb
      :title="data?.form?.name ?? t('labels.certificate')"
      :showAdd="false"
    />
  </div>

  <div v-if="loading" class="p-12 text-center text-gray-400 text-sm">
    Carregando respostas...
  </div>

  <div v-else-if="data" class="max-w-3xl mx-auto p-4 space-y-5 pb-12">

    <!-- Info geral -->
    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800 flex flex-wrap gap-6">
      <div>
        <span class="font-medium">{{ t('labels.vessel') }}:</span> {{ data.vessel.name }}
      </div>
      <div v-if="data.user">
        <span class="font-medium">{{ t('labels.responsible') }}:</span> {{ data.user.name }}
      </div>
      <div v-if="data.submitted_at">
        <span class="font-medium">{{ t('labels.date') }}:</span> {{ formatDate(data.submitted_at) }}
      </div>
    </div>

    <!-- Perguntas + respostas -->
    <div v-for="(q, index) in data.form.questions" :key="q.id" class="border rounded p-4 space-y-2">
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="font-medium text-sm">{{ index + 1 }}. {{ q.question }}</p>
          <p v-if="q.description" class="text-xs text-gray-500 mt-0.5">{{ q.description }}</p>
        </div>
        <span class="text-xs text-gray-400 shrink-0">{{ q.score }} pts</span>
      </div>

      <!-- Sem resposta -->
      <p v-if="q.answer === null && !q.file_url" class="text-sm text-gray-400 italic">
        {{ t('labels.no_answer') }}
      </p>

      <!-- Upload -->
      <div v-else-if="q.input_type === 'upload'">
        <a
          v-if="q.file_url"
          :href="q.file_url"
          target="_blank"
          class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
          </svg>
          {{ t('labels.view_file') }}
        </a>
        <span v-else class="text-sm text-gray-400 italic">{{ t('labels.no_answer') }}</span>
      </div>

      <!-- Radio / checkbox / select — show labels -->
      <div v-else-if="['radio', 'checkbox', 'select_simple', 'select_multiple'].includes(q.input_type)" class="space-y-1">
        <div class="text-sm text-gray-800">
          <template v-if="Array.isArray(q.answer)">
            <span v-for="val in q.answer" :key="val" class="inline-block bg-gray-100 rounded px-2 py-0.5 mr-1 text-xs">
              {{ optionLabel(q, val) }}
            </span>
          </template>
          <template v-else>
            <span class="inline-block bg-gray-100 rounded px-2 py-0.5 text-xs">
              {{ optionLabel(q, q.answer) }}
            </span>
          </template>
        </div>
        <p v-if="q.text_complement" class="text-xs text-gray-600 border-l-2 border-gray-300 pl-2">
          {{ q.text_complement }}
        </p>
      </div>

      <!-- Text / number / date -->
      <div v-else>
        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ q.answer }}</p>
        <p v-if="q.text_complement" class="text-xs text-gray-600 border-l-2 border-gray-300 pl-2 mt-1">
          {{ q.text_complement }}
        </p>
      </div>
    </div>

    <!-- Observações gerais -->
    <div v-if="data.observations" class="border rounded p-4">
      <p class="text-sm font-medium text-gray-700 mb-1">{{ t('labels.observations') }}</p>
      <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ data.observations }}</p>
    </div>

    <!-- Botões de ação -->
    <div class="flex justify-between items-center">
      <button
        type="button"
        @click="$router.push({ name: 'certificates' })"
        class="flex items-center gap-1 text-gray-600 hover:underline text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        {{ t('labels.return') }}
      </button>

      <div v-if="certificateStatus === 'completed'" class="flex gap-3">
        <button
          type="button"
          @click="openRejectModal"
          class="flex items-center gap-1 text-red-600 hover:underline text-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ t('labels.reject') }}
        </button>
        <button
          type="button"
          @click="approve"
          class="flex items-center gap-1 text-blue-600 hover:underline text-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          {{ t('labels.approve') }}
        </button>
      </div>
    </div>
  </div>

  <div v-else class="p-12 text-center text-gray-400 text-sm">
    Nenhuma resposta encontrada.
  </div>

  <!-- Modal de reprovação -->
  <div
    v-if="rejectModal.show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    @click.self="rejectModal.show = false"
  >
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 space-y-4">
      <h3 class="text-base font-semibold text-gray-800">{{ t('labels.reject_certificate') }}</h3>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('labels.rejection_reason') }}</label>
        <textarea
          v-model="rejectModal.reason"
          rows="4"
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
          :placeholder="t('labels.rejection_reason_placeholder')"
        />
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" @click="rejectModal.show = false" class="text-sm text-gray-600 hover:underline">
          {{ t('labels.cancel') }}
        </button>
        <button
          type="button"
          @click="confirmReject"
          :disabled="!rejectModal.reason.trim()"
          class="text-sm text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 px-4 py-1.5 rounded"
        >
          {{ t('labels.reject') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

import breadcrumb from '../../layout/Breadcrumb.vue'
import certificateService from '../../shared/api/services/certificate.service'
import { useAlertStore }   from '../../shared/store/alert'
import { useLoadingStore } from '../../shared/store/loading'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t }        = useI18n()
const route        = useRoute()
const router       = useRouter()
const alert        = useAlertStore()
const loadingStore = useLoadingStore()

const loading           = ref(true)
const data              = ref(null)
const certificateStatus = ref(null)
const rejectModal       = ref({ show: false, reason: '' })

onMounted(async () => {
  try {
    loadingStore.show()
    const [respRes, certRes] = await Promise.all([
      certificateService.getResponse(Number(route.params.id)),
      certificateService.getById(Number(route.params.id)),
    ])
    data.value              = respRes.data.data
    certificateStatus.value = certRes.data.data.status
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loading.value = false
    loadingStore.hide()
  }
})

function optionLabel(q, val) {
  return q.options?.find(o => o.value === val)?.label ?? val
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

async function approve() {
  try {
    loadingStore.show()
    await certificateService.approve(Number(route.params.id))
    alert.show(t('mensages.update'), 'success')
    router.push({ name: 'certificates' })
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loadingStore.hide()
  }
}

function openRejectModal() {
  rejectModal.value = { show: true, reason: '' }
}

async function confirmReject() {
  if (!rejectModal.value.reason.trim()) return
  try {
    loadingStore.show()
    rejectModal.value.show = false
    await certificateService.reject(Number(route.params.id), rejectModal.value.reason)
    alert.show(t('mensages.update'), 'success')
    router.push({ name: 'certificates' })
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loadingStore.hide()
  }
}
</script>
