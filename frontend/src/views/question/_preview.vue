<template>
  <div class="space-y-4">

    <div class="flex items-start gap-3 pb-3 border-b">
      <div class="flex-1">
        <p class="font-semibold text-gray-800 text-sm">{{ question.question }}</p>
        <p v-if="question.description" class="text-xs text-gray-500 mt-1">{{ question.description }}</p>
      </div>
      <span class="shrink-0 text-xs font-medium bg-blue-100 text-blue-700 px-2 py-1 rounded-full whitespace-nowrap">
        {{ t('labels.score') }}: {{ question.score }}
      </span>
    </div>

    <QuestionInput :question="question" />

    <div class="border-t pt-4 flex justify-end">
      <button
        type="button"
        class="flex items-center gap-1 text-blue-600 hover:underline text-sm"
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
import { useI18n } from "vue-i18n";
import QuestionInput from "../../components/QuestionInput.vue";
import { useModalStore } from "../../shared/store/modal";

const { t }  = useI18n();
const modal  = useModalStore();
const props  = defineProps(["question"]);

function closeModal() {
  modal.close();
}
</script>
