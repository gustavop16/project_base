<template>
  <div>
    <breadcrumb :title="form?.name ?? t('labels.certificate_form')" :showAdd="false" />
  </div>

  <div v-if="form" class="max-w-3xl mx-auto p-4 space-y-5 pb-12">

    <!-- Info do formulário -->
    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800 flex gap-6">
      <div v-if="form.vessel">
        <span class="font-medium">{{ t('labels.vessel') }}:</span> {{ form.vessel.name }}
      </div>
      <div v-if="form.description">{{ form.description }}</div>
    </div>

    <!-- Perguntas -->
    <div v-for="(q, index) in form.questions" :key="q.id" class="border rounded p-4 space-y-2">
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="font-medium text-sm">{{ index + 1 }}. {{ q.question }}</p>
          <p v-if="q.description" class="text-xs text-gray-500 mt-0.5">{{ q.description }}</p>
        </div>
        <span class="text-xs text-gray-400 shrink-0">{{ q.score }} pts</span>
      </div>

      <!-- text -->
      <textarea
        v-if="q.input_type === 'text'"
        v-model="answers[q.id].answer"
        rows="2"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <!-- number -->
      <input
        v-else-if="q.input_type === 'number'"
        type="number"
        v-model="answers[q.id].answer"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <!-- date -->
      <input
        v-else-if="q.input_type === 'date'"
        type="date"
        v-model="answers[q.id].answer"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <!-- upload -->
      <input
        v-else-if="q.input_type === 'upload'"
        type="file"
        @change="(e) => handleFileUpload(q.id, e)"
        class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
      />

      <!-- radio -->
      <div v-else-if="q.input_type === 'radio'" class="space-y-1">
        <label v-for="opt in q.options" :key="opt.value" class="flex items-center gap-2 cursor-pointer text-sm">
          <input type="radio" :name="`q_${q.id}`" :value="opt.value" v-model="answers[q.id].answer" class="accent-blue-600" />
          {{ opt.label }}
        </label>
        <textarea
          v-if="hasActiveTextInput(q)"
          v-model="answers[q.id].text_complement"
          rows="2"
          placeholder="Informe detalhes..."
          class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>

      <!-- checkbox -->
      <div v-else-if="q.input_type === 'checkbox'" class="space-y-1">
        <label v-for="opt in q.options" :key="opt.value" class="flex items-center gap-2 cursor-pointer text-sm">
          <input type="checkbox" :value="opt.value" v-model="answers[q.id].answer" class="accent-blue-600" />
          {{ opt.label }}
        </label>
        <textarea
          v-if="hasActiveTextInput(q)"
          v-model="answers[q.id].text_complement"
          rows="2"
          placeholder="Informe detalhes..."
          class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>

      <!-- select_simple -->
      <div v-else-if="q.input_type === 'select_simple'">
        <BaseSelect
          v-model="answers[q.id].answer"
          :options="q.options.map(o => ({ label: o.label, value: o.value }))"
        />
        <textarea
          v-if="hasActiveTextInput(q)"
          v-model="answers[q.id].text_complement"
          rows="2"
          placeholder="Informe detalhes..."
          class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>

      <!-- select_multiple (renderizado como checkboxes) -->
      <div v-else-if="q.input_type === 'select_multiple'" class="space-y-1">
        <label v-for="opt in q.options" :key="opt.value" class="flex items-center gap-2 cursor-pointer text-sm">
          <input type="checkbox" :value="opt.value" v-model="answers[q.id].answer" class="accent-blue-600" />
          {{ opt.label }}
        </label>
        <textarea
          v-if="hasActiveTextInput(q)"
          v-model="answers[q.id].text_complement"
          rows="2"
          placeholder="Informe detalhes..."
          class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>
    </div>

    <!-- Observações gerais -->
    <div class="border rounded p-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('labels.observations') }}</label>
      <textarea
        v-model="observations"
        rows="3"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />
    </div>

    <!-- Botões -->
    <div class="flex justify-end gap-4">
      <button
        type="button"
        @click="$router.back()"
        class="flex items-center gap-1 text-gray-600 hover:underline text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ t('labels.cancel') }}
      </button>
      <button
        type="button"
        @click="submit"
        class="flex items-center gap-1 text-blue-600 hover:underline text-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ t('labels.respond') }}
      </button>
    </div>
  </div>

  <div v-else class="p-12 text-center text-gray-400 text-sm">
    Carregando formulário...
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

import breadcrumb from '../../layout/Breadcrumb.vue'
import BaseSelect from '../../components/Base/BaseSelect.vue'

import certificateFormService from '../../shared/api/services/certificate_form.service'
import formResponseService    from '../../shared/api/services/form_response.service'
import { useAlertStore }   from '../../shared/store/alert'
import { useLoadingStore } from '../../shared/store/loading'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t }  = useI18n()
const route  = useRoute()
const router = useRouter()
const alert  = useAlertStore()
const loading = useLoadingStore()

const form         = ref(null)
const answers      = ref({})
const observations = ref('')

onMounted(async () => {
  try {
    loading.show()
    const res = await certificateFormService.getById(Number(route.params.id))
    form.value = res.data.data
    initAnswers()
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
})

function initAnswers() {
  const a = {}
  for (const q of (form.value?.questions ?? [])) {
    a[q.id] = {
      question_id:     q.id,
      answer:          ['checkbox', 'select_multiple'].includes(q.input_type) ? [] : '',
      text_complement: '',
    }
  }
  answers.value = a
}

function hasActiveTextInput(q) {
  const val = answers.value[q.id]?.answer
  if (!val && val !== 0) return false
  const selected = Array.isArray(val) ? val : [val]
  return q.options?.some(o => selected.includes(o.value) && o.has_text_input) ?? false
}

function handleFileUpload(questionId, event) {
  const file = event.target.files[0]
  if (file) answers.value[questionId].answer = file.name
}

async function submit() {
  try {
    loading.show()
    const payload = {
      certificate_form_id: form.value.id,
      observations:        observations.value || null,
      answers: Object.values(answers.value).map(a => ({
        question_id:     a.question_id,
        answer:          a.answer !== '' && a.answer !== null && !(Array.isArray(a.answer) && !a.answer.length)
                           ? a.answer : null,
        text_complement: a.text_complement || null,
      })),
    }
    await formResponseService.create(payload)
    alert.show(t('mensages.insert'), 'success')
    router.push({ name: 'certificate-form' })
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.hide()
  }
}
</script>
