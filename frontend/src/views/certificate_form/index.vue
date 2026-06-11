<template>
  <div>
    <breadcrumb class="mb-2"
      :title="t('labels.certificate_forms')"
      :showAdd="can('certificate_form.create')"
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
import { usePermissions } from '../../shared/composables/usePermissions'
import form    from './_form.vue'
import preview  from './_preview.vue'
import breadcrumb from '../../layout/Breadcrumb.vue'
import dataTable from '../../components/dataTable.vue'
import { useModalStore } from '../../shared/store/modal'
import certificateFormService from "../../shared/api/services/certificate_form.service";
import CertificateFormModel from '../../shared/api/models/CertificateFormModel';
import { useLoadingStore } from '../../shared/store/loading'
import { useAlertStore } from '../../shared/store/alert'
import { useDialogStore } from '../../shared/store/dialog'
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n()
const { can } = usePermissions()

const data = ref([]);

const modal   = useModalStore();
const loading = useLoadingStore();
const alert   = useAlertStore();
const dialog  = useDialogStore();

onMounted(() => {
  getAll()
})

const columns = [
  { label: t('labels.name'),            key: 'name'              },
  { label: t('labels.description'),     key: 'description_short' },
  { label: t('labels.questions_count'), key: 'questions_count'   },
]

const types_actions = computed(() => [
  { label: t("labels.view"),   type: "view",   permission: null                        },
  { label: t("labels.edit"),   type: "edit",   permission: 'certificate_form.update'   },
  { label: t("labels.delete"), type: "remove", permission: 'certificate_form.delete'   },
].filter(a => !a.permission || can(a.permission)));

const actions = {
  view:   (row) => view(row),
  edit:   (row) => edit(row),
  remove: (row) => remove(row.id),
};

function handleAction({ type, row }) {
  const action = actions[type];
  if (action) action(row);
}

async function getAll() {
  try {
    loading.show()
    const response = await certificateFormService.getAll();
    data.value = response.data.data.map(item => new CertificateFormModel(item));
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    loading.hide()
  }
}

function view(row) {
  modal.open(
    preview,
    { certificateForm: row },
    'large',
    t('labels.preview') + ' — ' + row.name,
    null
  )
}

function openModalCreate() {
  modal.open(form, {}, 'large', t('labels.new') + ' ' + t('labels.certificate_form'), fetchData)
}

function fetchData() {
  getAll()
}

function edit(row) {
  modal.open(form, { formUpdate: row }, 'large', t('labels.edit') + ' ' + t('labels.certificate_form'), fetchData)
}

function remove(id) {
  dialog.openDialog({
    dialogTitle:     t("labels.confirm"),
    dialogContent:   t("dialogs.delete"),
    confirmCallback: () => { destroy(id) },
  })
}

async function destroy(id) {
  try {
    loading.show()
    await certificateFormService.delete(id);
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
