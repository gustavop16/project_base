<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.name"             :label="t('labels.name')"             required />
      <BaseInput v-model="formData.call_sign"        :label="t('labels.call_sign')"        required />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.imo_number"       :label="t('labels.imo_number')"       required />
      <BaseInput v-model="formData.port_of_registry" :label="t('labels.port_of_registry')" />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.gross_tonnage"    :label="t('labels.gross_tonnage')"    type="number" />
      <BaseInput v-model="formData.built_at"         :label="t('labels.built_at')"         type="date" />
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
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput from "../../components/Base/BaseInput.vue";
import BaseCheckbox from "../../components/Base/BaseCheckbox.vue";

import vesselService from "../../shared/api/services/vessel.service";
import { useModalStore } from "../../shared/store/modal";
import { useAlertStore } from "../../shared/store/alert";
import { useLoadingStore } from "../../shared/store/loading";
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n();
const emit = defineEmits(["saved", "edited"]);
const props = defineProps(["formUpdate"]);
const modal = useModalStore();
const alert = useAlertStore();
const loading = useLoadingStore();

const formData = ref({
  id:               null,
  name:             "",
  call_sign:        "",
  port_of_registry: "",
  gross_tonnage:    "",
  built_at:         "",
  imo_number:       "",
  active:           true,
});

onMounted(() => {
  if (props.formUpdate?.id) {
    formData.value.id               = props.formUpdate.id;
    formData.value.name             = props.formUpdate.name             ?? "";
    formData.value.call_sign        = props.formUpdate.call_sign        ?? "";
    formData.value.port_of_registry = props.formUpdate.port_of_registry ?? "";
    formData.value.gross_tonnage    = props.formUpdate.gross_tonnage    ?? "";
    formData.value.built_at         = props.formUpdate.built_at instanceof Date
      ? props.formUpdate.built_at.toISOString().slice(0, 10)
      : (props.formUpdate.built_at ?? "");
    formData.value.imo_number       = props.formUpdate.imo_number       ?? "";
    formData.value.active           = props.formUpdate.active           ?? true;
  }
});

function closeModal() {
  modal.close();
}

async function submitForm() {
  if (formData.value.id) {
    edit();
  } else {
    create();
  }
}

async function create() {
  try {
    loading.show();
    await vesselService.create(formData.value);
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

    const input = new FormData();
    input.append("_method", "PUT");
    Object.entries(formData.value).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        input.append(key, value);
      }
    });

    await vesselService.update(formData.value.id, input);
    alert.show(t("mensages.update"), "success");
    emit("edited");
    modal.close();
  } catch (err) {
    emit("edited");
    alert.show(getErrorMessage(err.response), "error");
    console.error("Erro :", err);
  } finally {
    loading.hide();
  }
}
</script>
