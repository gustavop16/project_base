<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.description" :label="t('labels.description')" required/>
      <BaseInput v-model="formData.interval_days" :label="t('labels.interval_days')" type="number" required/>
    </div>

    <BaseUpload :required="false" :multiple="false" v-model:file="photo"></BaseUpload>

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
import { ref, onMounted, watch, watchEffect, toRaw, unref } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput from "../../components/Base/BaseInput.vue";
import BaseDateInput from "../../components/Base/BaseDateInput.vue";
import BaseSelect from "../../components/Base/BaseSelect.vue";
import BaseCheckbox from "../../components/Base/BaseCheckbox.vue";
import BaseMultiSelectTags from "../../components/Base/BaseMultiSelectTags.vue";
import BaseUpload from "../../components/Base/BaseUpload.vue";
import taskService from "../../shared/api/services/task.service";
import taskModel from "../../shared/api/models/TaskModel"
import clientService from "../../shared/api/services/client.service";
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
    id: "",
    description: "",
    interval_days: "",
});
const photo = ref(null) 
const realFile = unref(toRaw(photo.value))

const optionsClient = ref([]);


/* EVENTS ******************************/

onMounted(() => {
    getClient();
    if (props.formUpdate?.id) {
        formData.value.id           = (!props.formUpdate.id) ? null : props.formUpdate.id;
        formData.value.description  = (!props.formUpdate.description) ? null : props.formUpdate.description;
        formData.value.interval_days= (!props.formUpdate.interval_days) ? null : props.formUpdate.interval_days;
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
    await taskService.create(formData.value);
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
    input.append("description", formData.value.description);
    input.append("interval_days", formData.value.interval_days);
    input.append("photo", photo.value);
    input.append("_method", "PUT")
   
    // REQUISIÇÃO AO ENDPONT
    await taskService.update(formData.value.id, input);
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

async function getClient() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await clientService.getAll();
    optionsClient.value = response.data.data.map(item => ({
      value: item.id,
      label: item.name
    }))
    
  } catch (err) {
    //MOSTRA NOTIFICAÇÃO  ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide()
  }
}

</script>

<style scoped>
</style>
