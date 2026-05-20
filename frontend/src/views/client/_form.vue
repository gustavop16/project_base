<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.name" :label="t('labels.name')" required/>
      <BaseInput v-model="formData.responsible" :label="t('labels.responsible')"/>
    </div>

    <div class="grid grid-cols-3 gap-4">
      <BaseInput v-model="formData.document" :label="t('labels.document')" mask="cnpj" required/>
      <BaseInput v-model="formData.phone" :label="t('labels.phone')" mask="phone" />
      <BaseInput v-model="formData.email" :label="t('labels.email')"  type="email"/>
    </div>

     <BaseUpload :required="false" :multiple="false" v-model:file="formData.logo"></BaseUpload>

    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        class="flex items-center gap-1 text-blue-600 hover:underline"
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
import BaseCheckbox from "../../components/Base/BaseCheckbox.vue";
import BaseMultiSelectTags from "../../components/Base/BaseMultiSelectTags.vue";
import BaseUpload from "../../components/Base/BaseUpload.vue";

import clientService from "../../shared/api/services/client.service";
import clientModel from "../../shared/api/models/ClientModel"
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

// Dados
const formData = ref({
    name: "",
    document: "",
    responsible: "",
    phone: "",
    email: "",
   // logo: ""
}); 


/* EVENTS ******************************/

onMounted(() => {
  if (props.formUpdate?.id) {
    formData.value.id           = (!props.formUpdate.id) ? null : props.formUpdate.id;
    formData.value.name         = (!props.formUpdate.name) ? null : props.formUpdate.name;
    formData.value.document     = (!props.formUpdate.document) ? null : props.formUpdate.document;
    formData.value.responsible  = (!props.formUpdate.responsible) ? null : props.formUpdate.responsible;
    formData.value.phone        = (!props.formUpdate.phone) ? null : props.formUpdate.phone;
    formData.value.email        = (!props.formUpdate.email) ? null : props.formUpdate.email;
  }
});

/* FUNCTIONS ******************************/
function closeModal(){
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
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await clientService.create(formData.value);
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");
    //ENVIA RESPOSTA PARA ATULIZAR O DATATABLE
    emit("saved");
    //FECHA MODAL
    modal.close();
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

async function edit() {
  try {
    // ABRE LOADING
    loading.show();
    
    const input = new FormData();
    input.append("_method", "PUT")
    Object.entries(formData.value).forEach(([key, value]) => {
    if (value instanceof File || typeof value === "string" || typeof value === "number") {
        input.append(key, value);
    }
    });

    // REQUISIÇÃO AO ENDPONT
    await clientService.update(formData.value.id, input);
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
