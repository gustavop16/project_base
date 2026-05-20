<template>
  
  <button 
    class="flex items-center gap-1 text-gray-700 text-sm border rounded px-3 py-1.5 hover:bg-gray-100"
    @click="openModalcreate"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    {{ t("labels.new") }}
  </button>

  <data-table 
  :columns="columns" 
  :rows="data"
  :searchable = "false" 
  :types_actions="types_actions"
  @action="handleAction"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n'
import form from './_form.vue'
import dataTable  from '../../../components/dataTable.vue'
import { useModalStore } from '../../../shared/store/modal'
import taskProductService from "../../../shared/api/services/task_product.service.js";
import taskProductModel from '../../../shared/api/models/TaskProductModel';
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
const model      = route.meta.module;
const data = ref([]);

const columns = [
    { label: t('labels.task'), key: 'task', objectKey: 'description'},
    { label: t('labels.product'), key: 'product', objectKey: 'name'},
    { label: t('labels.quantity'), key: 'amount'},
]


const types_actions = [
  { label: t("labels.edit"),   type: "edit" },
  { label:  t("labels.delete"), type: "remove" },
];
     
const actions = {
  edit: (row) => edit(row),
  remove: (row) => remove(row.id),
};

function handleAction({ type, row }) {
  const action = actions[type];
  if (action) {
    action(row);
  } else {
    console.warn(`Ação não encontrada: ${type}`);
  }
}

onMounted(() => {
  getAll()
})

/****FUNCTIONS ****************** */

async function getAll() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await taskProductService.getByTask(id_current);
    //PREENCHE O DATATABLE
    data.value = response.data.data.map(item => new taskProductModel(item));
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

function openModalcreate() {
  modal.open(
    form, 
    {}, 
    'large',
    t('labels.new')+' '+t('labels.taskProduct'),
    fetchData
  )
}
async function fetchData(){
  getAll()
}

function edit(row) {
  modal.open(
    form, 
    {formUpdate : row }, 
    'large',
    t('labels.edit')+' '+t('labels.taskProduct'),
    fetchData
  )
}

function remove(id) {
    dialog.openDialog({
        dialogTitle: t("labels.confirm") ,
        dialogContent: t("dialogs.delete"),
        confirmCallback: () => {
            destroy(id)
        },
    })
}

async function destroy(id) {
    try {
        // ABRE LOADING
        loading.show()
        // REQUISIÇÃO AO ENDPOINT
        await taskProductService.delete(id);
        //MOSTRA NOTIFICAÇÃO SUCESSO
        alert.show(t("mensages.delete"), 'success')
        //ATULIZAR O DATATABLE
        data.value = data.value.filter(item => item.id !== id)
    } catch (err) {
        //NOTIFICATION ERRO
        alert.show(t("mensages.error")+' '+err, 'error')
        console.error("Erro :", err);
    } finally {
        // FINALIZA LOADING
        loading.hide()
    }
}

async function editStatus(id, status) {
  try {
    // ABRE LOADING
    loading.show();
    var status = (status == 'active') ? "inactive" : "active";
    // REQUISIÇÃO AO ENDPONT
    await taskProductService.updateStatus(
      id,
      {status : status}
    );
    getAll()
    //NOTIFICATION SUCESSO
    alert.show(t("mensages.update"), "success");
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

function disable(id, status) {
  var dialogs = (status == 'active') ? t("dialogs.disable") : t("dialogs.enable");
    dialog.openDialog({
        dialogTitle: t("labels.confirm") ,
        dialogContent: dialogs,
        confirmCallback: () => {
            editStatus(id, status)
        },
    })
}
</script>

<style scoped>

</style>