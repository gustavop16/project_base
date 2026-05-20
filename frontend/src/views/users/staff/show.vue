<template>
 <div class="flex flex-col">
    <breadcrumb class="mb-2"
      :title="t('labels.users')"
      :linkText ="t('labels.staff')"
      linkHref="/usuarios/usuarios"
      :showExport = false
      :showAdd = true
      @export="exportarDados"
      @add="openModalcreate"
    />
  
    <div class="relative bg-gray-50 overflow-x-auto pl-2">
        <BaseShowData
        :columns="columns"
        :records="Array(dataUser)"
        :types_actions="types_actions"
        @action="handleAction"
        @attachments="openAttachments"
        ></BaseShowData>
    </div>

</div>
</template>

<script setup>
  import { ref, onMounted, onBeforeUnmount } from "vue";
  import { useRoute, useRouter } from 'vue-router';
  import { useI18n } from "vue-i18n";
  import breadcrumb from '../../../layout/Breadcrumb.vue' 
  import { useLoadingStore } from "../../../shared/store/loading";
  import { useModalStore } from '../../../shared/store/modal'
  import userService from "../../../shared/api/services/user.service";
  import { useAlertStore } from '../../../shared/store/alert'
  import { useAuthStore } from '../../../shared/store/auth'
  import formUser from './../staff/_form.vue'
  import formPass from '../account/password.vue'
  import BaseAttachments from "../../../components/Base/BaseAttachments.vue";
  import { useDialogStore } from '../../../shared/store/dialog'
  import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'
  import BaseShowData from "../../../components/Base/BaseShowData.vue";
  
  const router  = useRouter();
  const route   = useRoute();  
  const { t }   = useI18n();
  const loading = useLoadingStore();
  const alert   = useAlertStore();
  const modal   = useModalStore();
  const dialog  = useDialogStore();
  const auth    = useAuthStore();

  const dropdownRef = ref<HTMLElement | null>(null)
  const isDropdownOpen = ref(false);
  const dataUser  = ref({});
  const id_current = route.params.id;


/* EVENTS ******************************/
onMounted(() => {
  getById();
});

onBeforeUnmount(() => {
 
})

/* CONFIG LIST INF ******************************/
const columns = [
  { label: t("labels.name"), key: "name" },
  { label: t("Status"), key: "status_txt" },
  { label: t("labels.document"), key: "document" },
  { label: t("labels.birthdate"), key: "birthdate" },
  { label: t("labels.phone"), key: "phone" },
  { label: t("labels.email"), key: "email" },
  { label: t("labels.country"), key: "country" },
  { label: t("labels.state"), key: "state" },
  { label: t("labels.city"), key: "city" },
  { label: t("labels.address"), key: "address" },
  { label: t("labels.zipcode"), key: "zipcode" },
  { label: t("labels.department"), key: "department", objectKey:"name" },
  { label: t("labels.role"), key: "role", objectKey:"name" },
  { label: t("labels.company"), key: "company" },
  { label: t("labels.station"), key: "station" },
  { label: t("labels.facility"), key: "facility" },
  { label: t("labels.bay"), key: "bay" },
  { label: t("labels.remarks"), key: "remarks" },
  
];

const types_actions = [
  { label: t("labels.edit"),    type: "edit" },
  { label:  t("labels.delete"), type: "remove" },
  { label: labelActive().label , type: "disable" },

];

const actions = {
  edit: (row) => edit(),
  remove: (row) => remove(),
  disable: (row) => disable(),

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
    const response = await userService.getById(id_current);
    dataUser.value = response.data.data
    console.log(dataUser.value);
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

function openAttachments() {
  modal.open(
    BaseAttachments, 
    { model: 'users', item_id: id_current }, 
    'large',
    t('labels.add')+' '+t('labels.attachments'),
    () => {}
  )
}

function edit() {
  modal.open(
    formUser, 
    {formUpdate : dataUser.value }, 
    'large',
    t('labels.edit')+' '+t('labels.user'),
    fetchUsers
  )
}

function openModalcreate() {
  modal.open(
    formUser, 
    {}, 
    'large',
    t('labels.new')+' '+t('labels.user'),
    () => {
        router.push({ name: 'user-staff' });
    }
  )
}

function fetchUsers(){
  getById()
}

function labelActive(){
  var label       = "Habilitar";
  var statusClass = "hover:bg-red-50 text-red-600 hover:text-red-700";

  if(dataUser.value.status == 'active'){
    label       = "Desabilitar";
    statusClass = "hover:bg-blue-50 text-blue-600 hover:text-blue-700"
  }
  return {"label": label, "statusClass": statusClass};

}


async function editStatus(id, status) {
  try {
    // ABRE LOADING
    loading.show();
    var status = (status == 'active') ? "inactive" : "active";
    // REQUISIÇÃO AO ENDPONT
    await userService.update(
      id,
      {status : status}
    );
    getById()
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

function disable() {
  var dialogs = (dataUser.value.status == 'active') ? t("dialogs.disable") : t("dialogs.enable");
    dialog.openDialog({
        dialogTitle: t("labels.confirm") ,
        dialogContent: dialogs,
        confirmCallback: () => {
            editStatus(dataUser.value.id, dataUser.value.status)
        },
    })
}

function remove() {
    dialog.openDialog({
        dialogTitle: t("labels.confirm") ,
        dialogContent: t("dialogs.delete"),
        confirmCallback: () => {
            destroy(id_current)
        },
    })
}

async function destroy(id) {
    try {
        // ABRE LOADING
        loading.show()
        // REQUISIÇÃO AO ENDPOINT
        await userService.delete(id);
        //MOSTRA NOTIFICAÇÃO SUCESSO
        alert.show(t("mensages.delete"), 'success');
        router.push({ name: 'user-staff' });
    } catch (err) {
        //NOTIFICATION ERRO
        alert.show(t("mensages.error")+' '+err, 'error')
        console.error("Erro :", err);
    } finally {
        // FINALIZA LOADING
        loading.hide()
    }
}

</script>