<template>
  <div class="flex flex-col">
    
    <div class="bg-gray-50 overflow-x-auto pl-2">
        <BaseShowData
        :columns="columns"
        :records="Array(data)"
        :types_actions="types_actions"
        @action="handleAction"
      ></BaseShowData>

    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import breadcrumb from "../../layout/Breadcrumb.vue";
import { useLoadingStore } from "../../shared/store/loading";
import { useModalStore } from "../../shared/store/modal";
import { useAlertStore } from "../../shared/store/alert";
import taskService from "../../shared/api/services/task.service.js";
import taskModel from "../../shared/api/models/TaskModel";
import form from "./_form.vue";
import { useDialogStore } from "../../shared/store/dialog";
import BaseShowData from "../../components/Base/BaseShowData.vue";
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const router  = useRouter();
const route = useRoute();
const id_current = route.params.id;

const { t } = useI18n();
const loading = useLoadingStore();
const alert = useAlertStore();
const modal = useModalStore();
const dialog = useDialogStore();
const emit = defineEmits(["saved", "edited"]);
const dropdownRef = (ref < HTMLElement) | (null > null);
const isDropdownOpen = ref(false);
const data = ref({});
const dataAttachments = ref([]);

/* EVENTS ******************************/
onMounted(() => {
  getById();
});

onBeforeUnmount(() => {
});

/* CONFIG LIST INF ******************************/
const columns = [
    { label: t('labels.description'), key: 'description'},
    { label: t('labels.interval_days'), key: 'interval_days' },
];

const types_actions = [
  { label: t("labels.edit"), type: "edit" },
  { label: t("labels.delete"), type: "remove" },
];

const actions = {
  edit: (row) => edit(),
  remove: (row) => remove(),
};

function handleAction({ type, row }) {
  const action = actions[type];
  if (action) {
    action(row);
  } else {
    console.warn(`Ação não encontrada: ${type}`);
  }
}

/* FUNCTIONS ******************************/
async function getById() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await taskService.getById(id_current);
    data.value = response.data.data;
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

function fetchData(){
   getById()
}


function openModalcreate() {
  modal.open(
    form, 
    {}, 
    'large',
    t('labels.new')+' '+t('labels.company'),
    () => {
        router.push({ name: 'facility-company' });
    }
  )
}

function edit() {
  modal.open(
    form,
    { formUpdate: data.value },
    "large",
    t("labels.edit") + " " + t("labels.company"),
    fetchData
  );
}

function remove() {
  dialog.openDialog({
    dialogTitle: t("labels.confirm"),
    dialogContent: t("dialogs.delete"),
    confirmCallback: () => {
      destroy(id_current);
    },
  });
}

async function destroy(id) {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPOINT
    await taskService.delete(id);
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.delete"), "success");
    router.back()
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

</script>