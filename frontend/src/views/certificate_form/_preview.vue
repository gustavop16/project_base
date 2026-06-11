<template>
  <div class="space-y-5">

    <!-- Header do formulário -->
    <div class="pb-3 border-b">
      <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
          <h2 class="font-semibold text-gray-800">{{ formData.name }}</h2>
          <p v-if="formData.description" class="text-xs text-gray-500 mt-1">{{ formData.description }}</p>
        </div>
        <span v-if="!isLoading" class="shrink-0 text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded-full whitespace-nowrap">
          {{ formData.questions.length }} {{ t('labels.form_questions') }}
        </span>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex items-center justify-center py-10 text-gray-400 text-sm gap-2">
      <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
      </svg>
      Carregando perguntas...
    </div>

    <!-- Lista de perguntas renderizadas -->
    <div v-else class="space-y-4">
      <div
        v-for="(q, index) in formData.questions"
        :key="q.id"
        class="border rounded-lg p-4 space-y-3 bg-white"
      >
        <!-- Cabeçalho da pergunta -->
        <div class="flex items-start gap-3">
          <span class="shrink-0 w-6 h-6 bg-gray-200 text-gray-600 text-xs font-bold rounded-full flex items-center justify-center mt-0.5">
            {{ index + 1 }}
          </span>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-gray-800 text-sm">{{ q.question }}</p>
            <p v-if="q.description" class="text-xs text-gray-500 mt-0.5">{{ q.description }}</p>
          </div>
          <span class="shrink-0 text-xs bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5 rounded-full whitespace-nowrap">
            {{ q.score }} pts
          </span>
        </div>

        <!-- Input renderizado -->
        <div class="pl-9">
          <QuestionInput :question="q" />
        </div>
      </div>

      <p v-if="!formData.questions.length" class="text-center text-sm text-gray-400 py-8">
        Nenhuma pergunta cadastrada neste formulário.
      </p>
    </div>

    <!-- Rodapé -->
    <div class="border-t pt-4 flex justify-end gap-3">
      <button
        type="button"
        class="flex items-center gap-1 text-gray-500 hover:underline text-sm"
        @click="closeModal"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Fechar
      </button>
     
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

import QuestionInput from "../../components/QuestionInput.vue";
import certificateFormService from "../../shared/api/services/certificate_form.service";
import { useModalStore } from "../../shared/store/modal";
import { useAlertStore } from "../../shared/store/alert";
import { getErrorMessage } from "../../shared/utils/getErrorMessage.js";

const { t }  = useI18n();
const router = useRouter();
const modal  = useModalStore();
const alert  = useAlertStore();
const props  = defineProps(["certificateForm"]);

const isLoading = ref(true);
const formData  = ref({
  name:        props.certificateForm?.name ?? '',
  description: props.certificateForm?.description ?? '',
  questions:   [],
});

onMounted(async () => {
  try {
    const res = await certificateFormService.getById(props.certificateForm.id);
    const data = res.data.data;
    formData.value.name        = data.name        ?? '';
    formData.value.description = data.description ?? '';
    formData.value.questions   = [...(data.questions ?? [])].sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
  } catch (err) {
    alert.show(getErrorMessage(err?.response), 'error');
  } finally {
    isLoading.value = false;
  }
});

function closeModal() {
  modal.close();
}

function goToFill() {
  modal.close();
  router.push({ name: 'certificate-form-fill', params: { id: props.certificateForm.id } });
}
</script>
