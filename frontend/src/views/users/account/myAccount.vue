<template>
  <div class="relative">
    <BaseShowData
      :columns="columns"
      :records="Array(dataUser)"
      :types_actions="types_actions"
      @action="handleAction"
      @attachments="openAttachments"
      dynamicMaxHeight="max-h-[calc(150vh-272px-48px)]"
    ></BaseShowData>

    

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";
import { useLoadingStore } from "../../../shared/store/loading";
import { useModalStore } from "../../../shared/store/modal";
import { useDialogStore } from '../../../shared/store/dialog'
import userService from "../../../shared/api/services/user.service";
import { useAlertStore } from "../../../shared/store/alert";
import { useAuthStore } from "../../../shared/store/auth";
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'
import formAttachment from "./attachment.vue";
import formUser from "./../staff/_form.vue";
import formPhoto from "./photo.vue";
import formPass from "./password.vue";
import BaseAttachments from "../../../components/Base/BaseAttachments.vue";
import BaseShowData from "../../../components/Base/BaseShowData.vue";


const { t } = useI18n();
const loading = useLoadingStore();
const alert = useAlertStore();
const modal = useModalStore();
const auth = useAuthStore();
const dialog = useDialogStore();

const dropdownRef = (ref < HTMLElement) | (null > null);
const isDropdownOpen = ref(false);
const dataUser = ref([]);

/* EVENTS ******************************/
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  getById();
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

/* CONFIG LIST INF ******************************/

const columns = [
{ label: t("labels.user"), key: "user_code" },
  { label: t("labels.name"), key: "name" },
  { label: t("Status"), key: "status_txt" },
  { label: t("labels.document"), key: "document" },
  { label: t("labels.birthdate"), key: "birthdate_br" },
  { label: t("labels.education"), key: "educations"},
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
  { label: t("labels.skills"), key: "skills"},
  { label: t("labels.remarks"), key: "remarks"},
];

const types_actions = [
  { label: "Editar", type: "edit" },
  { label: "Alterar senha", type: "password" },
  { label: "Alterar foto", type: "updatePhoto" },
  { label: "Remover foto", type: "deletePhoto" },
];

const actions = {
  edit: (row) => edit(),
  password: (row) => openChangePassword(),
  updatePhoto: (row) => openChangePhoto(),
  deletePhoto: (row) => removePhoto(),
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
function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false;
  }
}

async function getById() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await userService.getById(auth.user_id);
    dataUser.value = response.data.data;
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

function fetchUser(){
  getById()
}

function openAttachments() {
  modal.open(
    BaseAttachments,
    { model: "users", item_id: auth.user_id },
    "large",
    t("labels.add") + " " + t("labels.attachments"),
    () => {}
  );
}

function edit() {
  modal.open(
    formUser,
    { formUpdate: dataUser.value },
    "large",
    t("labels.edit") + " " + t("labels.user"),
    fetchUser
  );
}

function openChangePhoto() {
  modal.open(
    BaseAttachments,
    { model: "profiles", item_id: auth.user_id, multiple: false },
    "small",
    t("labels.edit") + " " + t("labels.photo"),
    () => {}
  );
}

function openChangePassword() {
  modal.open(
    formPass,
    {},
    "small",
    t("labels.change") + " " + t("labels.password"),
    () => {}
  );
}

function removePhoto() {
    dialog.openDialog({
        dialogTitle: t("labels.confirm") ,
        dialogContent: t("dialogs.delete_photo"),
        confirmCallback: () => {
            updatePhoto(dataUser.value.id)
        },
    })
}

async function updatePhoto(id) {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await userService.update(
      id,
      {photo : null}
    );
    getById()
    //NOTIFICATION SUCESSO
    alert.show(t("mensages.update"), "success");
    localStorage.setItem('user_photo', '')
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    window.location.reload();
  }
}
</script>