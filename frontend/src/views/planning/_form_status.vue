<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-3 gap-4">
      <BaseSelect v-model="formData.status" :label="t('labels.status')" :options="optionsStatus" required/>
      <BaseInput v-model="formData.responsible_name" :label="t('labels.responsible')"/>
        <BaseInput v-model="formData.executing_company" :label="t('labels.executing_company')"/>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <BaseDateInput v-model="formData.execution_date" :label="t('labels.execution_date')"/>
        <BaseInput v-model="formData.certification" :label="t('labels.certification')"/>
    </div>
    <BaseTextarea v-model="formData.observations" :label="t('labels.observations')" />
    
    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        class="flex items-center gap-1 text-blue-600 hover:underline"
        @click="onSave"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M5 13l4 4L19 7"
          />
        </svg>
        Save
      </button>

      <button
        class="flex items-center gap-1 text-blue-600 hover:underline"
        @click="closeModal"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watch, watchEffect } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput from "../../components/Base/BaseInput.vue";
import BaseDateInput from "../../components/Base/BaseDateInput.vue";
import BaseSelect from "../../components/Base/BaseSelect.vue";
import BaseTextarea from "../../components/Base/BaseTextarea.vue";
import planningService from "../../shared/api/services/planning.service";

import { useModalStore } from "../../shared/store/modal";
import { useAlertStore } from "../../shared/store/alert";
import { useLoadingStore } from "../../shared/store/loading";
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n();
const emit = defineEmits(["saved", "edited"]);
const props = defineProps(["item"]);
const modal = useModalStore();
const alert = useAlertStore();
const loading = useLoadingStore();

// Dados
const formData = ref({
    planning_id: "",
    status: "",
    responsible_name: "",
    executing_company: "",
    execution_date: "",
    observations: "",
    certification: ""
}); 

const optionsStatus = ref([
    {label: "Realizada", value: "carried_out"},
    {label: "Pendente certificação", value: "pending_certification"},
]);

/* EVENTS ******************************/

onMounted(() => {
    //console.log('props.formUpdate', props.item)
    if (props.item?.id) {
        formData.value.planning_id = (props.item.id) ?? null;
    }
    //console.log('formData.value', formData.value)
});

/* FUNCTIONS ******************************/
function closeModal(){
  modal.close();
}

async function submitForm() {
    updateStatus();
}

async function updateStatus() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await planningService.updateStatus(formData.value.planning_id, formData.value);
    //NOTIFICATION SUCESSO
    alert.show(t("mensages.update"), "success");
    emit("edited");
    //FECHA MODAL
    modal.close();
  } catch (err) {
    //NOTIFICATION ERRO
    emit("edited");
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

</script>

<style scoped>
</style>
