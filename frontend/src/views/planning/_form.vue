<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-3 gap-4">
      <BaseSelect v-model="formData.client_id" :label="t('labels.client')" :options="optionsClient" @change="getPlace" required/>
      <BaseSelect v-model="formData.place_id" :label="t('labels.place')" :options="optionsPlace" required/>
      <BaseSelect v-model="formData.task_id" :label="t('labels.task')" :options="optionsTask" required/>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <BaseInput v-model="formData.responsible_name" :label="t('labels.responsible')" required/>
        <BaseInput v-model="formData.executing_company" :label="t('labels.executing_company')" required/>
        <BaseDateInput v-model="formData.execution_date" :label="t('labels.execution_date')" required/>
    </div>
    <BaseTextarea v-model="formData.observations" :label="t('labels.observations')" required/>
    
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
import clientService from "../../shared/api/services/client.service";
import taskService from "../../shared/api/services/task.service";
import placeService from "../../shared/api/services/place.service";
import planningModel from "../../shared/api/models/PlanningModel"
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
    client_id: "",
    task_id: "",
    place_id: "",
    status: "",
    responsible_name: "",
    executing_company: "",
    execution_date: "",
    observations: "",
    status: "planned"
}); 

const client_id = ref('');

const optionsClient = ref([]);
const optionsTask = ref([]);
const optionsPlace = ref([]);


/* EVENTS ******************************/

onMounted(() => {
    getClient();
    getTask();
    
    if (props.formUpdate?.id) {
        formData.value.id           = (!props.formUpdate.id) ? null : props.formUpdate.id;
        formData.value.client_id    = (!props.formUpdate.client?.id) ? null : props.formUpdate.client.id;
        formData.value.task_id      = (!props.formUpdate.task?.id) ? null : props.formUpdate.task.id;
        formData.value.place_id     = (!props.formUpdate.place?.id) ? null : props.formUpdate.place.id;
        formData.value.status           = (!props.formUpdate.status) ? null : props.formUpdate.status;
        formData.value.responsible_name = (!props.formUpdate.responsible_name) ? null : props.formUpdate.responsible_name;
        formData.value.executing_company= (!props.formUpdate.executing_company) ? null : props.formUpdate.executing_company;
        formData.value.execution_date   = (!props.formUpdate.status) ? null : props.formUpdate.execution_date;
        formData.value.observations     = (!props.formUpdate.observations) ? null : props.formUpdate.observations;
        getPlace();
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
    await planningService.create(formData.value);
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
    // REQUISIÇÃO AO ENDPONT
    await planningService.update(formData.value.id,formData.value);
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

async function getTask() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await taskService.getAll();

    console.log('task', response.data.data)

    optionsTask.value = response.data.data.map(item => ({
      value: item.id,
      label: item.description
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

async function getPlace() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await placeService.getByClient(formData.value.client_id);
    optionsPlace.value = response.data.data.map(item => ({
      value: item.id,
      label: item.place
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
