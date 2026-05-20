<template>
  <BaseAttachment @onSave="updatePhoto" :required="true" :multiple="false"></BaseAttachment>
</template>


<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import fileDownload from "js-file-download";
import BaseAttachment from "../../../components/Base/BaseAttachments.vue";
import { useLoadingStore } from "../../../shared/store/loading";
import attachmentService from "../../../shared/api/services/attachment.service";
import userService from "../../../shared/api/services/user.service";
import { useAlertStore } from "../../../shared/store/alert";
import { useDialogStore } from "../../../shared/store/dialog";
import { useAuthStore } from '../../../shared/store/auth'
import { useModalStore } from "../../../shared/store/modal"
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'


const { t } = useI18n();

const loading = useLoadingStore();
const alert = useAlertStore();
const dialog = useDialogStore();
const auth   = useAuthStore();
const modal = useModalStore();

onMounted(() => {
})

/****FUNCTIONS ****************** */

async function updatePhoto(formData) {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await userService.updatePhoto(auth.user_id, formData.value);
    const user = response.data.data;
    var newPhoto = 'images/no-photo.png';
    if(user.photo){
      newPhoto = import.meta.env.VITE_APP_URL+'storage/profile/'+user.id+'/'+user.photo
    }
    localStorage.setItem('user_photo', newPhoto)
    auth.user_photo = newPhoto;
    formData.value.files = [];
    modal.close();
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}

function deleteFile(file_id) {
  dialog.openDialog({
    dialogTitle: t("labels.confirm"),
    dialogContent: t("dialogs.delete"),
    confirmCallback: () => {
      destroy(file_id);
    },
  });
}

async function destroy(id) {
  loading.show();
  try {
    await attachmentService.delete(id);
    alert.show(t("mensages.delete"), "success");
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  }
  loading.hide();
}

</script>