<template>
  <form @submit.prevent="submitForm" class="space-y-3">

    <BaseInput
      v-model="formData.question"
      :label="t('labels.question')"
      required
    />

    <BaseTextarea
      v-model="formData.description"
      :label="t('labels.description')"
      :rows="3"
    />

    <div class="grid grid-cols-2 gap-4">
      <BaseInput
        v-model.number="formData.score"
        :label="t('labels.score')"
        type="number"
        required
      />
      <BaseSelect
        v-model="formData.input_type"
        :label="t('labels.input_type')"
        :options="inputTypeOptions"
        required
      />
    </div>

    <!-- Opções (select, radio, checkbox) -->
    <div v-if="requiresOptions" class="border rounded p-3 space-y-2">
      <div class="flex items-center justify-between mb-1">
        <p class="text-sm font-medium text-gray-700">{{ t('labels.options') }}</p>
        <button
          type="button"
          @click="addOption"
          class="flex items-center gap-1 text-xs text-blue-600 hover:underline"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('labels.add_option') }}
        </button>
      </div>

      <!-- Cabeçalho -->
      <div v-if="formData.options.length" class="grid gap-2 px-1" style="grid-template-columns: 1fr auto auto">
        <span class="text-xs text-gray-500">{{ t('labels.option_label') }}</span>
        <span class="text-xs text-gray-500 whitespace-nowrap">Abre campo texto</span>
        <span></span>
      </div>

      <!-- Linhas de opções -->
      <div
        v-for="(option, index) in formData.options"
        :key="index"
        class="grid gap-2 items-center"
        style="grid-template-columns: 1fr auto auto"
      >
        <BaseInput v-model="option.label" :placeholder="t('labels.option_label')" />
        <label class="flex items-center justify-center cursor-pointer" title="Ao selecionar esta opção, abre um campo de texto">
          <input type="checkbox" v-model="option.has_text_input" class="w-4 h-4 rounded accent-blue-600" />
        </label>
        <button
          type="button"
          @click="removeOption(index)"
          class="text-red-500 hover:text-red-700 text-lg leading-none"
          title="Remover"
        >
          &times;
        </button>
      </div>

      <p v-if="!formData.options.length" class="text-xs text-gray-400 text-center py-2">
        Nenhuma opção adicionada.
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

import questionService from "../../shared/api/services/question.service";
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

const TYPES_WITH_OPTIONS = ['select_simple', 'select_multiple', 'radio', 'checkbox'];

const inputTypeOptions = [
  { label: 'Texto livre',        value: 'text'            },
  { label: 'Numérico',           value: 'number'          },
  { label: 'Data',               value: 'date'            },
  { label: 'Select simples',     value: 'select_simple'   },
  { label: 'Select múltiplo',    value: 'select_multiple' },
  { label: 'Radio button',       value: 'radio'           },
  { label: 'Checkbox',           value: 'checkbox'        },
  { label: 'Upload de arquivo',  value: 'upload'          },
];

const formData = ref({
  id:          null,
  question:    "",
  description: "",
  score:       0,
  input_type:  "",
  options:     [],
});

const requiresOptions = computed(() =>
  TYPES_WITH_OPTIONS.includes(formData.value.input_type)
);

onMounted(() => {
  if (props.formUpdate?.id) {
    formData.value.id          = props.formUpdate.id;
    formData.value.question    = props.formUpdate.question    ?? "";
    formData.value.description = props.formUpdate.description ?? "";
    formData.value.score       = props.formUpdate.score       ?? 0;
    formData.value.input_type  = props.formUpdate.input_type  ?? "";
    formData.value.options     = (props.formUpdate.options ?? []).map(o => ({ label: o.label, has_text_input: o.has_text_input ?? false }));
  }
});

function addOption() {
  formData.value.options.push({ label: '', has_text_input: false });
}

function removeOption(index) {
  formData.value.options.splice(index, 1);
}

function closeModal() {
  modal.close();
}

function buildPayload() {
  return {
    question:    formData.value.question,
    description: formData.value.description,
    score:       formData.value.score,
    input_type:  formData.value.input_type,
    options:     requiresOptions.value
      ? formData.value.options.map(o => ({ label: o.label, value: o.label, has_text_input: o.has_text_input ?? false }))
      : [],
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
    await questionService.create(buildPayload());
    alert.show(t("mensages.insert"), "success");
    emit("saved");
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error");
    console.error("Erro :", err);
  } finally {
    loading.hide();
  }
}

async function edit() {
  try {
    loading.show();
    await questionService.update(formData.value.id, { ...buildPayload(), _method: 'PUT' });
    alert.show(t("mensages.update"), "success");
    emit("edited");
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error");
    console.error("Erro :", err);
  } finally {
    loading.hide();
  }
}
</script>
