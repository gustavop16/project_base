<template>
  <form @submit.prevent="submitForm" class="space-y-3">

    <BaseInput
      v-model="formData.name"
      :label="t('labels.name')"
      required
    />

    <BaseTextarea
      v-model="formData.description"
      :label="t('labels.description')"
      :rows="3"
    />

    <!-- Perguntas do formulário -->
    <div class="border rounded p-3 space-y-2">
      <p class="text-sm font-medium text-gray-700">{{ t('labels.form_questions') }}</p>

      <!-- Picker -->
      <div class="flex gap-2 items-end">
        <div class="flex-1">
          <BaseSelect
            v-model="selectedQuestionId"
            :options="availableQuestionOptions"
            :placeholder="'Selecione uma pergunta...'"
          />
        </div>
        <button
          type="button"
          @click="addQuestion"
          :disabled="!selectedQuestionId"
          class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-40 whitespace-nowrap"
        >
          + {{ t('labels.add_question') }}
        </button>
      </div>

      <!-- Lista ordenada -->
      <div v-if="formData.questions.length" class="space-y-1 mt-1">
        <div
          v-for="(q, index) in formData.questions"
          :key="q.id"
          class="flex items-center gap-2 p-2 bg-gray-50 rounded border text-sm"
        >
          <span class="text-xs font-bold text-gray-400 w-5 text-center shrink-0">{{ index + 1 }}</span>
          <div class="flex-1 min-w-0">
            <p class="font-medium truncate">{{ q.question }}</p>
            <p class="text-xs text-gray-500">Score: {{ q.score }}</p>
          </div>
          <div class="flex gap-0.5 shrink-0">
            <button
              type="button"
              @click="moveUp(index)"
              :disabled="index === 0"
              class="px-1 py-0.5 text-gray-500 hover:text-gray-700 disabled:opacity-30 text-base leading-none"
              title="Mover para cima"
            >↑</button>
            <button
              type="button"
              @click="moveDown(index)"
              :disabled="index === formData.questions.length - 1"
              class="px-1 py-0.5 text-gray-500 hover:text-gray-700 disabled:opacity-30 text-base leading-none"
              title="Mover para baixo"
            >↓</button>
          </div>
          <button
            type="button"
            @click="removeQuestion(index)"
            class="text-red-500 hover:text-red-700 text-lg leading-none shrink-0"
            title="Remover"
          >&times;</button>
        </div>
      </div>

      <p v-else class="text-xs text-gray-400 text-center py-2">
        Nenhuma pergunta adicionada.
      </p>
    </div>

    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        type="submit"
        class="flex items-center gap-1 text-blue-600 hover:underline"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        Save
      </button>
      <button
        type="button"
        class="flex items-center gap-1 text-blue-600 hover:underline"
        @click="closeModal"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput    from "../../components/Base/BaseInput.vue";
import BaseTextarea from "../../components/Base/BaseTextarea.vue";
import BaseSelect   from "../../components/Base/BaseSelect.vue";

import certificateFormService from "../../shared/api/services/certificate_form.service";
import questionService        from "../../shared/api/services/question.service";
import { useModalStore }   from "../../shared/store/modal";
import { useAlertStore }   from "../../shared/store/alert";
import { useLoadingStore } from "../../shared/store/loading";
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js';

const { t } = useI18n();
const emit  = defineEmits(["saved", "edited"]);
const props = defineProps(["formUpdate"]);

const modal   = useModalStore();
const alert   = useAlertStore();
const loading = useLoadingStore();

const allQuestions       = ref([]);
const selectedQuestionId = ref('');

const formData = ref({
  id:          null,
  name:        '',
  description: '',
  active:      true,
  questions:   [],
});

const availableQuestionOptions = computed(() => {
  const selectedIds = formData.value.questions.map(q => Number(q.id));
  return allQuestions.value
    .filter(q => !selectedIds.includes(Number(q.id)))
    .map(q => ({ label: q.question, value: q.id }));
});

onMounted(async () => {
  try {
    loading.show();

    const qRes = await questionService.getAll();
    allQuestions.value = qRes.data.data;

    if (props.formUpdate?.id) {
      const fRes = await certificateFormService.getById(props.formUpdate.id);
      const form = fRes.data.data;
      formData.value.id          = form.id;
      formData.value.name        = form.name        ?? '';
      formData.value.description = form.description ?? '';
      formData.value.active      = form.active      ?? true;
      formData.value.questions   = [...(form.questions ?? [])].sort((a, b) => a.order - b.order);
    }
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error');
    console.error('Erro:', err);
  } finally {
    loading.hide();
  }
});

function addQuestion() {
  const id = Number(selectedQuestionId.value);
  if (!id) return;
  const question = allQuestions.value.find(q => Number(q.id) === id);
  if (question) {
    formData.value.questions.push({ ...question });
    selectedQuestionId.value = '';
  }
}

function removeQuestion(index) {
  formData.value.questions.splice(index, 1);
}

function moveUp(index) {
  if (index === 0) return;
  const qs = formData.value.questions;
  [qs[index - 1], qs[index]] = [qs[index], qs[index - 1]];
}

function moveDown(index) {
  const qs = formData.value.questions;
  if (index >= qs.length - 1) return;
  [qs[index + 1], qs[index]] = [qs[index], qs[index + 1]];
}

function closeModal() {
  modal.close();
}

function buildPayload() {
  return {
    name:        formData.value.name,
    description: formData.value.description,
    active:      formData.value.active,
    questions:   formData.value.questions.map(q => ({ id: q.id })),
  };
}

async function submitForm() {
  if (formData.value.id) {
    await edit();
  } else {
    await create();
  }
}

async function create() {
  try {
    loading.show();
    await certificateFormService.create(buildPayload());
    alert.show(t('mensages.insert'), 'success');
    emit('saved');
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error');
    console.error('Erro:', err);
  } finally {
    loading.hide();
  }
}

async function edit() {
  try {
    loading.show();
    await certificateFormService.update(formData.value.id, { ...buildPayload(), _method: 'PUT' });
    alert.show(t('mensages.update'), 'success');
    emit('edited');
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), 'error');
    console.error('Erro:', err);
  } finally {
    loading.hide();
  }
}
</script>
