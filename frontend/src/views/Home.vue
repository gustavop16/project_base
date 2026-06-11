<template>
  <div class="p-4">

    <!-- ── Dashboard admin ───────────────────────────────────────────── -->
    <template v-if="can('certificates.approve')">

      <!-- Cabeçalho -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
          <p class="text-xs text-gray-400 mt-0.5">
            Atualiza em {{ countdown }}s
            <button @click="refresh" class="ml-2 text-blue-600 hover:underline">Atualizar agora</button>
          </p>
        </div>
        <!-- Toggle cards / lista -->
        <div class="flex gap-1 border rounded overflow-hidden">
          <button
            @click="setView('card')"
            :class="view === 'card' ? 'bg-blue-900 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
            class="px-3 py-1.5 text-sm flex items-center gap-1 transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Cards
          </button>
          <button
            @click="setView('list')"
            :class="view === 'list' ? 'bg-blue-900 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'"
            class="px-3 py-1.5 text-sm flex items-center gap-1 transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Lista
          </button>
        </div>
      </div>

      <!-- Estatísticas -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white border rounded-lg p-4 flex items-center gap-3">
          <div class="bg-blue-100 rounded-full p-2 shrink-0">
            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.total }}</p>
            <p class="text-xs text-gray-500">Total</p>
          </div>
        </div>
        <div class="bg-white border rounded-lg p-4 flex items-center gap-3">
          <div class="bg-yellow-100 rounded-full p-2 shrink-0">
            <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.pending }}</p>
            <p class="text-xs text-gray-500">Pendentes</p>
          </div>
        </div>
        <div class="bg-white border rounded-lg p-4 flex items-center gap-3">
          <div class="bg-blue-100 rounded-full p-2 shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.awaiting }}</p>
            <p class="text-xs text-gray-500">Ag. aprovação</p>
          </div>
        </div>
        <div class="bg-white border rounded-lg p-4 flex items-center gap-3">
          <div class="bg-green-100 rounded-full p-2 shrink-0">
            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.approved }}</p>
            <p class="text-xs text-gray-500">Aprovados</p>
          </div>
        </div>
        <div class="bg-white border rounded-lg p-4 flex items-center gap-3">
          <div class="bg-red-100 rounded-full p-2 shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold text-gray-800">{{ stats.rejected }}</p>
            <p class="text-xs text-gray-500">Reprovados</p>
          </div>
        </div>
      </div>

      <!-- Título da seção -->
      <div class="flex items-center gap-2 mb-4">
        <span v-if="stats.awaiting > 0" class="bg-blue-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
          {{ stats.awaiting }}
        </span>
      </div>

      <!-- Loading -->
      <div v-if="isLoading" class="text-center py-16 text-gray-400 text-sm">Carregando...</div>

      <!-- Sem pendências -->
      <div v-else-if="awaiting.length === 0" class="text-center py-16 text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm">Nenhum certificado aguardando aprovação.</p>
      </div>

      <!-- ── Visualização em cards ────────────────────────────────────── -->
      <div v-else-if="view === 'card'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div
          v-for="cert in awaiting"
          :key="cert.id"
          class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col gap-3"
        >
          <!-- Topo do card -->
          <div class="flex items-start justify-between gap-2">
            <div>
              <p class="text-xs text-gray-400 mb-0.5">Nº {{ String(cert.id).padStart(6, '0') }}</p>
              <p class="font-medium text-gray-800 text-sm leading-snug">{{ cert.form_name }}</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border bg-blue-100 text-blue-800 border-blue-300 shrink-0">
              Ag. aprovação
            </span>
          </div>

          <!-- Dados -->
          <div class="space-y-1 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l9 6 9-6M3 6v12a2 2 0 002 2h14a2 2 0 002-2V6"/>
              </svg>
              {{ cert.vessel_name }}
            </div>
            <div v-if="cert.created_at" class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ formatDate(cert.created_at) }}
            </div>
          </div>

          <!-- Ações -->
          <div class="flex gap-2 mt-auto pt-2 border-t border-gray-100">
            <button
              @click="openHistory(cert)"
              class="flex-1 text-xs text-gray-600 border border-gray-200 rounded px-2 py-1.5 hover:bg-gray-50 transition"
            >
              Histórico
            </button>
            <button
              @click="goToResponse(cert)"
              class="flex-1 text-xs text-gray-600 border border-gray-200 rounded px-2 py-1.5 hover:bg-gray-50 transition"
            >
              Ver respostas
            </button>
            <button
              @click="openRejectModal(cert)"
              class="flex-1 text-xs text-red-600 border border-red-200 rounded px-2 py-1.5 hover:bg-red-50 transition"
            >
              Reprovar
            </button>
            <button
              @click="approve(cert)"
              class="flex-1 text-xs text-white bg-green-600 rounded px-2 py-1.5 hover:bg-green-700 transition"
            >
              Aprovar
            </button>
          </div>
        </div>
      </div>

      <!-- ── Visualização em lista ────────────────────────────────────── -->
      <div v-else class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
              <th class="p-3">Nº</th>
              <th class="p-3">Formulário</th>
              <th class="p-3">Navio</th>
              <th class="p-3">Enviado em</th>
              <th class="p-3 text-right">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cert in awaiting" :key="cert.id" class="border-t hover:bg-gray-50">
              <td class="p-3 text-gray-400 text-xs">{{ String(cert.id).padStart(6, '0') }}</td>
              <td class="p-3 font-medium">{{ cert.form_name }}</td>
              <td class="p-3">{{ cert.vessel_name }}</td>
              <td class="p-3 text-gray-400 text-xs">{{ formatDate(cert.created_at) }}</td>
              <td class="p-3 text-right">
                <div class="flex justify-end gap-2">
                  <button @click="openHistory(cert)" class="text-xs text-gray-600 border rounded px-2 py-1 hover:bg-gray-50">Histórico</button>
                  <button @click="goToResponse(cert)" class="text-xs text-gray-600 border rounded px-2 py-1 hover:bg-gray-50">Ver</button>
                  <button @click="openRejectModal(cert)" class="text-xs text-red-600 border border-red-200 rounded px-2 py-1 hover:bg-red-50">Reprovar</button>
                  <button @click="approve(cert)" class="text-xs text-white bg-green-600 rounded px-2 py-1 hover:bg-green-700">Aprovar</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- ── Tela de boas-vindas (não-admin) ────────────────────────────── -->
    <template v-else>
      <div class="flex flex-col items-center justify-center h-[60vh]">
        <img id="logo" :src="appConfig.logo.home" alt="Logo" class="mb-6 opacity-10 scale-75" />
        <h1 class="text-2xl font-semibold text-gray-700">Bem-vindo ao {{ appConfig.name }}</h1>
        <p class="text-sm text-gray-400 mt-2">Use o menu lateral para navegar.</p>
      </div>
    </template>

    <!-- ── Modal de reprovação ─────────────────────────────────────────── -->
    <div
      v-if="rejectModal.show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
      @click.self="rejectModal.show = false"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 space-y-4">
        <h3 class="text-base font-semibold text-gray-800">Reprovar Certificado</h3>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da reprovação</label>
          <textarea
            v-model="rejectModal.reason"
            rows="4"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-300"
            placeholder="Descreva o motivo da reprovação..."
          />
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="rejectModal.show = false" class="text-sm text-gray-600 hover:underline">Cancelar</button>
          <button
            type="button"
            @click="confirmReject"
            :disabled="!rejectModal.reason.trim()"
            class="text-sm text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 px-4 py-1.5 rounded"
          >Reprovar</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { appConfig } from './../config/app'
import { usePermissions } from './../shared/composables/usePermissions'
import { useAlertStore }   from './../shared/store/alert'
import { useLoadingStore } from './../shared/store/loading'
import { useModalStore }   from './../shared/store/modal'
import certificateService from './../shared/api/services/certificate.service'
import CertificateModel   from './../shared/api/models/CertificateModel'
import { getErrorMessage } from './../shared/utils/getErrorMessage.js'
import historyModal from './certificate/_history.vue'

const router  = useRouter()
const { can } = usePermissions()
const alert   = useAlertStore()
const loading = useLoadingStore()
const modal   = useModalStore()

const REFRESH_SECONDS = 300
const VIEW_KEY = 'dashboard_view'

const data        = ref([])
const isLoading   = ref(false)
const countdown   = ref(REFRESH_SECONDS)
const view        = ref(localStorage.getItem(VIEW_KEY) ?? 'card')
const rejectModal = ref({ show: false, row: null, reason: '' })

let timer    = null
let interval = null

const awaiting = computed(() => data.value.filter(c => c.status === 'completed'))

const stats = computed(() => ({
  total:    data.value.length,
  pending:  data.value.filter(c => c.status === 'pending').length,
  awaiting: awaiting.value.length,
  approved: data.value.filter(c => c.status === 'approved').length,
  rejected: data.value.filter(c => c.status === 'rejected').length,
}))

function setView(v) {
  view.value = v
  localStorage.setItem(VIEW_KEY, v)
}

function formatDate(val) {
  if (!val) return '—'
  return new Date(val).toLocaleDateString('pt-BR')
}

async function refresh() {
  if (!can('certificates.approve')) return
  try {
    isLoading.value = true
    const response = await certificateService.getAll()
    data.value = response.data.data.map(item => new CertificateModel(item))
    countdown.value = REFRESH_SECONDS
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    isLoading.value = false
  }
}

function startAutoRefresh() {
  interval = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      refresh()
    }
  }, 1000)
}

onMounted(() => {
  refresh()
  startAutoRefresh()
})

onUnmounted(() => {
  clearInterval(interval)
  clearTimeout(timer)
})

function goToResponse(cert) {
  router.push({ name: 'certificates-response', params: { id: cert.id } })
}

function openHistory(cert) {
  modal.open(historyModal, { certificateId: cert.id }, 'medium', 'Histórico — ' + cert.form_name)
}

async function approve(cert) {
  try {
    loading.show()
    const response = await certificateService.approve(cert.id)
    const updated = new CertificateModel(response.data.data)
    const idx = data.value.findIndex(c => c.id === cert.id)
    if (idx !== -1) data.value[idx] = updated
    alert.show('Certificado aprovado com sucesso!', 'success')
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loading.hide()
  }
}

function openRejectModal(cert) {
  rejectModal.value = { show: true, row: cert, reason: '' }
}

async function confirmReject() {
  const { row, reason } = rejectModal.value
  if (!reason.trim()) return
  try {
    loading.show()
    rejectModal.value.show = false
    const response = await certificateService.reject(row.id, reason)
    const updated = new CertificateModel(response.data.data)
    const idx = data.value.findIndex(c => c.id === row.id)
    if (idx !== -1) data.value[idx] = updated
    alert.show('Certificado reprovado.', 'success')
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
  } finally {
    loading.hide()
  }
}
</script>
