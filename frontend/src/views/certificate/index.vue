<template>
  <div>
    <breadcrumb class="mb-2"
      :title="t('labels.certificates')"
      :showAdd="can('certificates.create')"
      @add="openModalCreate"
    />
  </div>
  <data-table
    :columns="columns"
    :rows="data"
    :types_actions="types_actions"
    @action="handleAction"
  />

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
        <button
          type="button"
          @click="rejectModal.show = false"
          class="text-sm text-gray-600 hover:underline"
        >{{ t('labels.cancel') }}</button>
        <button
          type="button"
          @click="confirmReject"
          :disabled="!rejectModal.reason.trim()"
          class="text-sm text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 px-4 py-1.5 rounded"
        >{{ t('labels.reject') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import form        from './_form.vue'
import historyModal from './_history.vue'
import breadcrumb from '../../layout/Breadcrumb.vue'
import dataTable  from '../../components/dataTable.vue'
import { useModalStore }  from '../../shared/store/modal'
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore }  from '../../shared/store/alert'
import { useDialogStore } from '../../shared/store/dialog'
import certificateService from '../../shared/api/services/certificate.service'
import CertificateModel   from '../../shared/api/models/CertificateModel'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'
import { usePermissions } from '../../shared/composables/usePermissions'

const { t }  = useI18n()
const router = useRouter()
const { can } = usePermissions()

const data = ref([])

const modal   = useModalStore()
const loading = useLoadingStore()
const alert   = useAlertStore()
const dialog  = useDialogStore()

const rejectModal = ref({ show: false, row: null, reason: '' })

onMounted(() => getAll())

const STATUS_BADGE = {
  pending:   'bg-yellow-100 text-yellow-800 border-yellow-300',
  completed: 'bg-blue-100  text-blue-800  border-blue-300',
  approved:  'bg-green-100 text-green-800 border-green-300',
  rejected:  'bg-red-100   text-red-800   border-red-300',
}

const columns = [
  { label: t('labels.form_response'), key: 'form_name'   },
  { label: t('labels.vessel'),        key: 'vessel_name' },
  {
    label:      t('labels.status'),
    key:        'status_label',
    badge:      true,
    badgeClass: (row) => STATUS_BADGE[row.status] ?? 'bg-gray-100 text-gray-700 border-gray-300',
  },
]

const types_actions = computed(() => [
  {
    label:     t('labels.respond'),
    type:      'respond',
    condition: (row) => row.status === 'pending' && !can('certificates.approve') && can('form_response.create'),
  },
  {
    label:     t('labels.view'),
    type:      'view_response',
    condition: (row) => row.status !== 'pending' && can('certificates.approve'),
  },
  /*{
    label:     t('labels.approve'),
    type:      'approve',
    condition: (row) => row.status === 'completed' && can('certificates.approve'),
  },
  {
    label:     t('labels.reject'),
    type:      'reject',
    condition: (row) => row.status === 'completed' && can('certificates.approve'),
  },*/
  {
    label:     t('labels.download_pdf'),
    type:      'download',
    condition: (row) => row.status === 'approved',
  },
  {
    label:     t('labels.history'),
    type:      'history',
    condition: () => true,
  },
  {
    label:     t('labels.delete'),
    type:      'remove',
    condition: (row) => !can('certificates.approve') && can('certificates.delete'),
  },
])

const actions = {
  respond:       (row) => router.push({ name: 'certificates-respond',  params: { id: row.id } }),
  view_response: (row) => router.push({ name: 'certificates-response', params: { id: row.id } }),
  approve:       (row) => approveCertificate(row),
  reject:        (row) => openRejectModal(row),
  history:       (row) => modal.open(historyModal, { certificateId: row.id }, 'medium', t('labels.history') + ' — ' + row.form_name),
  download:      (row) => downloadPdf(row),
  remove:        (row) => remove(row.id),
}

function handleAction({ type, row }) {
  const action = actions[type]
  if (action) action(row)
}

async function getAll() {
  try {
    loading.show()
    const response = await certificateService.getAll()
    data.value = response.data.data.map(item => new CertificateModel(item))
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}

function openModalCreate() {
  modal.open(form, {}, 'large', t('labels.new') + ' ' + t('labels.certificate'), fetchData)
}

function fetchData() {
  getAll()
}

function remove(id) {
  dialog.openDialog({
    dialogTitle:     t('labels.confirm'),
    dialogContent:   t('dialogs.delete'),
    confirmCallback: () => { destroy(id) },
  })
}

async function destroy(id) {
  try {
    loading.show()
    await certificateService.delete(id)
    alert.show(t('mensages.delete'), 'success')
    data.value = data.value.filter(item => item.id !== id)
  } catch (err) {
    alert.show(t('mensages.error') + ' ' + err, 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}

async function approveCertificate(row) {
  try {
    loading.show()
    const response = await certificateService.approve(row.id)
    const updated = new CertificateModel(response.data.data)
    const idx = data.value.findIndex(item => item.id === row.id)
    if (idx !== -1) data.value[idx] = updated
    alert.show(t('mensages.update'), 'success')
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}

function openRejectModal(row) {
  rejectModal.value = { show: true, row, reason: '' }
}

async function confirmReject() {
  const { row, reason } = rejectModal.value
  if (! reason.trim()) return
  try {
    loading.show()
    rejectModal.value.show = false
    const response = await certificateService.reject(row.id, reason)
    const updated = new CertificateModel(response.data.data)
    const idx = data.value.findIndex(item => item.id === row.id)
    if (idx !== -1) data.value[idx] = updated
    alert.show(t('mensages.update'), 'success')
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}

async function downloadPdf(row) {
  try {
    loading.show()
    const response = await certificateService.downloadPdf(row.id)
    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `certificado_${String(row.id).padStart(6, '0')}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}
</script>
