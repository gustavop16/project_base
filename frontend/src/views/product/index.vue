<template>
  <div>
    <breadcrumb class="mb-2"
      :title="t('labels.products')"
      :showAdd = true
      @add="openModalcreate"
    />
  </div>
  <data-table 
  :columns="columns" 
  :rows="data" 
  :types_actions="types_actions"
  @action="handleAction"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from 'vue-i18n'
import form from './_form.vue'
import breadcrumb from '../../layout/Breadcrumb.vue' 
import dataTable  from '../../components/dataTable.vue'
import { useModalStore } from '../../shared/store/modal'
import productService from "../../shared/api/services/product.service";
import productModel from '../../shared/api/models/ProductModel';
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore } from '../../shared/store/alert'
import { useDialogStore } from '../../shared/store/dialog'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n()

const data = ref([]);

const modal = useModalStore();
const loading = useLoadingStore()
const alert = useAlertStore()
const dialog = useDialogStore()

onMounted(() => {
  getAll()
})

/* CONFIG DATATABLE ******************************/
const columns = [
    { label: t('labels.name'), key: 'name', isLink: 'product-show' },
    { label: t('labels.description'), key: 'description' },
    { label: t('labels.unit'), key: 'unit' },
    { label: t('labels.manufacturer'), key: 'manufacturer' },
    { label: t('labels.certification'), key: 'certification' },
    { label: t('labels.classification'), key: 'classification' },
    { label: t('labels.technical_standard'), key: 'technical_standard' },
    { label: t('labels.sales_cost'), key: 'sales_cost' },
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

/****FUNCTIONS ****************** */
async function getAll() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await productService.getAll();
    console.log('data',response.data.data)
    //PREENCHE O DATATABLE
    data.value = response.data.data.map(item => new productModel(item));
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
    t('labels.new')+' '+t('labels.product'),
    fetchData
  )
}
function fetchData(){
   getAll()
}

function edit(row) {
  modal.open(
    form, 
    {formUpdate : row }, 
    'large',
    t('labels.edit')+' '+t('labels.product'),
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
        await productService.delete(id);
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
    await productService.update(
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
