<template>
  <div>
    <breadcrumb :title="data?.form?.name ?? t('labels.certificate')" :showAdd="false" />
  </div>

  <div v-if="loading" class="p-12 text-center text-gray-400 text-sm">
    Carregando formulário...
  </div>

  <div v-else-if="data" class="max-w-3xl mx-auto p-4 space-y-6 pb-12">

    <!-- Cabeçalho: título e descrição do formulário -->
    <div class="border-b border-gray-200 pb-4">
      <h1 class="text-2xl font-bold text-[#1e3a5f]">{{ data.form.name }}</h1>
      <p v-if="data.form.description" class="mt-1 text-sm text-gray-500">{{ data.form.description }}</p>
    </div>

    <!-- Dados do navio -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
      <div class="bg-[#1e3a5f] px-4 py-2">
        <h2 class="text-xs font-bold uppercase tracking-wider text-white">{{ t('labels.vessel') }}</h2>
      </div>
      <div class="p-4 grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
        <div>
          <span class="text-gray-500">{{ t('labels.name') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.name || '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">{{ t('labels.imo_number') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.imo_number || '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">{{ t('labels.call_sign') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.call_sign || '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">{{ t('labels.port_of_registry') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.port_of_registry || '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">{{ t('labels.gross_tonnage') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.gross_tonnage ? Number(data.vessel.gross_tonnage).toLocaleString('pt-BR') + ' t' : '—' }}</p>
        </div>
        <div>
          <span class="text-gray-500">{{ t('labels.built_at') }}</span>
          <p class="font-medium text-gray-800">{{ data.vessel.built_at || '—' }}</p>
        </div>
      </div>
    </div>

    <!-- Banner de reprovação -->
    <div v-if="data.status === 'rejected'" class="bg-red-50 border border-red-300 rounded-lg p-4 text-sm text-red-800 space-y-1">
      <p class="font-semibold">{{ t('labels.certificate_rejected') }}</p>
      <p>{{ data.rejected_reason }}</p>
    </div>

    <!-- Perguntas -->
    <div v-for="(q, index) in data.form.questions" :key="q.id" class="bg-white border border-gray-200 rounded-lg p-4 space-y-3">
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="font-medium text-sm text-gray-800">{{ index + 1 }}. {{ q.question }}</p>
          <p v-if="q.description" class="text-xs text-gray-400 mt-0.5">{{ q.description }}</p>
        </div>
        <span class="text-xs text-gray-400 shrink-0">{{ q.score }} pts</span>
      </div>

      <textarea
        v-if="q.input_type === 'text'"
        v-model="answers[q.id].answer"
        rows="2"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <input
        v-else-if="q.input_type === 'number'"
        type="number"
        v-model="answers[q.id].answer"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <input
        v-else-if="q.input_type === 'date'"
        type="date"
        v-model="answers[q.id].answer"
        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
      />

      <input
        v-else-if="q.input_type === 'upload'"
        type="file"
        @change="(e) => handleFileUpload(q.id, e)"
        class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
      />

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

      <div v-else-if="q.input_type === 'select_simple'">
        <select
          v-model="answers[q.id].answer"
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        >
          <option disabled value="">Selecione</option>
          <option v-for="opt in q.options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
        <textarea
          v-if="hasActiveTextInput(q)"
          v-model="answers[q.id].text_complement"
          rows="2"
          placeholder="Informe detalhes..."
          class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-300"
        />
      </div>

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
    <div class="bg-white border border-gray-200 rounded-lg p-4">
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
        @click="$router.push({ name: 'certificates' })"
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
    Formulário não encontrado.
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

const loading      = ref(true)
const data         = ref(null)
const answers      = ref({})
const observations = ref('')

onMounted(async () => {
  try {
    loadingStore.show()
    const res = await certificateService.getForm(Number(route.params.id))
    data.value = res.data.data
    initAnswers()
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
    console.error('Erro:', err)
  } finally {
    loading.value = false
    loadingStore.hide()
  }
})

function initAnswers() {
  const a = {}
  for (const q of (data.value?.form?.questions ?? [])) {
    a[q.id] = {
      question_id:     q.id,
      answer:          ['checkbox', 'select_multiple'].includes(q.input_type) ? [] : '',
      text_complement: '',
      file:            null,
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
  if (file) {
    answers.value[questionId].file   = file
    answers.value[questionId].answer = file.name
  }
}

async function submit() {
  try {
    loadingStore.show()

    const formData = new FormData()
    formData.append('observations', observations.value || '')

    const answersPayload = Object.values(answers.value).map(a => ({
      question_id:     a.question_id,
      answer:          a.file
                         ? null
                         : (a.answer !== '' && a.answer !== null && !(Array.isArray(a.answer) && !a.answer.length)
                             ? a.answer : null),
      text_complement: a.text_complement || null,
    }))
    formData.append('answers', JSON.stringify(answersPayload))

    for (const [questionId, ans] of Object.entries(answers.value)) {
      if (ans.file) {
        formData.append(`files[${questionId}]`, ans.file)
      }
    }

    await certificateService.submitResponse(Number(route.params.id), formData)
    alert.show(t('mensages.insert'), 'success')
    router.push({ name: 'certificates' })
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error')
    console.error('Erro:', err)
  } finally {
    loadingStore.hide()
  }
}
</script>
