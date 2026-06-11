<template>
  <div>
    <breadcrumb class="mb-2"
      :title="t('labels.vessels')"
      :showAdd="can('vessel.create')"
      @add="openModalCreate"
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
import { ref, computed, onMounted } from "vue";
import { useI18n } from 'vue-i18n'
import form from './_form.vue'
import breadcrumb from '../../layout/Breadcrumb.vue'
import dataTable from '../../components/dataTable.vue'
import { useModalStore } from '../../shared/store/modal'
import vesselService from "../../shared/api/services/vessel.service";
import VesselModel from '../../shared/api/models/VesselModel';
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore } from '../../shared/store/alert'
import { useDialogStore } from '../../shared/store/dialog'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'
import { usePermissions } from '../../shared/composables/usePermissions'

const { t } = useI18n()
const { can } = usePermissions()

const data = ref([]);

const modal = useModalStore();
const loading = useLoadingStore();
const alert = useAlertStore();
const dialog = useDialogStore();

onMounted(() => {
  getAll()
})

/* CONFIG DATATABLE ******************************/
const columns = [
  { label: t('labels.name'),             key: 'name' },
  { label: t('labels.call_sign'),        key: 'call_sign' },
  { label: t('labels.imo_number'),       key: 'imo_number' },
  { label: t('labels.port_of_registry'), key: 'port_of_registry' },
  { label: t('labels.gross_tonnage'),    key: 'gross_tonnage' },
  { label: t('labels.built_at'),         key: 'built_at' },
]

const types_actions = computed(() => [
  { label: t("labels.edit"),   type: "edit",   permission: 'vessel.update' },
  { label: t("labels.delete"), type: "remove", permission: 'vessel.delete' },
].filter(a => !a.permission || can(a.permission)));

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
    loading.show()
    const response = await vesselService.getAll();
    data.value = response.data.data.map(item => new VesselModel(item));
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    loading.hide()
  }
}

function openModalCreate() {
  modal.open(
    form,
    {},
    'large',
    t('labels.new') + ' ' + t('labels.vessel'),
    fetchData
  )
}

function fetchData() {
  getAll()
}

function edit(row) {
  modal.open(
    form,
    { formUpdate: row },
    'large',
    t('labels.edit') + ' ' + t('labels.vessel'),
    fetchData
  )
}

function remove(id) {
  dialog.openDialog({
    dialogTitle: t("labels.confirm"),
    dialogContent: t("dialogs.delete"),
    confirmCallback: () => {
      destroy(id)
    },
  })
}

async function destroy(id) {
  try {
    loading.show()
    await vesselService.delete(id);
    alert.show(t("mensages.delete"), 'success')
    data.value = data.value.filter(item => item.id !== id)
  } catch (err) {
    alert.show(t("mensages.error") + ' ' + err, 'error')
    console.error("Erro :", err);
  } finally {
    loading.hide()
  }
}
</script>
