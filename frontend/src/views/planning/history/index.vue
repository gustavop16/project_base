<template>
  
  <data-table 
  :columns="columns" 
  :rows="data"
  :searchable = "false" 
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'

import dataTable  from '../../../components/dataTable.vue'
import { useModalStore } from '../../../shared/store/modal'
import historyService from "../../../shared/api/services/history_planning.service.js";
import HistoryPlanningModel from '../../../shared/api/models/HistoryPlanningModel';
import { useLoadingStore } from '../../../shared/store/loading'
import { useAlertStore } from '../../../shared/store/alert'
import { useDialogStore } from '../../../shared/store/dialog'
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'

const { t } = useI18n()
const modal   = useModalStore();
const loading = useLoadingStore()
const alert   = useAlertStore()
const dialog  = useDialogStore()
const route = useRoute();

const id_current = route.params.id;

const data = ref([]);

const columns = [
    { label: t('labels.status'), key: 'status' },
    { label: t('labels.client'), key: 'client' },
    { label: t('labels.task'),   key: 'task' },
    { label: t('labels.place'),  key: 'place' },
    { label: t('labels.responsible_name'),  key: 'responsible_name'},
    { label: t('labels.executing_company'), key: 'executing_company'},
    { label: t('labels.execution_date'),    key: 'execution_date_br'},
    { label: t('labels.observations'),      key: 'observations'},
]

onMounted(() => {
  getAll()
})

/****FUNCTIONS ****************** */

async function getAll() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await historyService.getAll(id_current);
    console.log('response',response)
    //PREENCHE O DATATABLE
    data.value = response.data.data.map(item => new HistoryPlanningModel(item));
    //data = response.data.data
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