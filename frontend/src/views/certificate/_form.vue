<template>
  <form @submit.prevent="submitForm" class="space-y-4">

    <BaseSelect
      v-model="formData.certificate_form_id"
      :label="t('labels.certificate_form')"
      :options="formOptions"
      :placeholder="'Selecione um formulário...'"
      required
    />

    <BaseSelect
      v-model="formData.vessel_id"
      :label="t('labels.vessel')"
      :options="vesselOptions"
      :placeholder="'Selecione um navio...'"
      required
    />

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          {{ t('labels.monitoring_start') }} <span class="text-red-500">*</span>
        </label>
        <input
          v-model="formData.monitoring_start"
          type="date"
          required
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          {{ t('labels.monitoring_end') }} <span class="text-red-500">*</span>
        </label>
        <input
          v-model="formData.monitoring_end"
          type="date"
          :min="formData.monitoring_start || undefined"
          required
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>
    </div>

    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        type="submit"
        class="flex items-center gap-1 text-blue-600 hover:underline"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ t('labels.confirm') }}
      </button>
      <button
        type="button"
        class="flex items-center gap-1 text-gray-600 hover:underline"
        @click="closeModal"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ t('labels.cancel') }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

import BaseSelect   from '../../components/Base/BaseSelect.vue'

import certificateService     from '../../shared/api/services/certificate.service'
import certificateFormService from '../../shared/api/services/certificate_form.service'
import vesselService          from '../../shared/api/services/vessel.service'
import { useModalStore }   from '../../shared/store/modal'
import { useAlertStore }   from '../../shared/store/alert'
import { useLoadingStore } from '../../shared/store/loading'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n()
const emit  = defineEmits(['saved'])

const modal   = useModalStore()
const alert   = useAlertStore()
const loading = useLoadingStore()

const allForms   = ref([])
const allVessels = ref([])

const formData = ref({
  certificate_form_id: '',
  vessel_id:           '',
  monitoring_start:    '',
  monitoring_end:      '',
})

const formOptions = computed(() =>
  allForms.value.map(f => ({ label: f.name, value: f.id }))
)

const vesselOptions = computed(() =>
  allVessels.value.map(v => ({ label: v.name, value: v.id }))
)

onMounted(async () => {
  try {
    loading.show()
    const [fRes, vRes] = await Promise.all([
      certificateFormService.getAll(),
      vesselService.getAll(true),
    ])
    allForms.value   = fRes.data.data
    allVessels.value = vRes.data.data
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
})

function closeModal() {
  modal.close()
}

async function submitForm() {
  try {
    loading.show()
    await certificateService.create({
      certificate_form_id: Number(formData.value.certificate_form_id),
      vessel_id:           Number(formData.value.vessel_id),
      monitoring_start:    formData.value.monitoring_start,
      monitoring_end:      formData.value.monitoring_end,
    })
    alert.show(t('mensages.insert'), 'success')
    emit('saved')
    modal.close()
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}
</script>
