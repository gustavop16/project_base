<template>
  <BaseAttachment :required="true" :multiple="true" @onSave="create"></BaseAttachment>

  <div class="divide-y divide-gray-200 dark:divide-black">
    <div
      v-for="(item, index) in dataAttachments"
      :key="index"
      class="py-3 flex justify-between"
    >
      <p class="text-gray-700 dark:text-gray-400">
        <button @click="openFile(item.file)" class="hover:text-primary" href="">
          {{ item.name ?? "Anexo " + (index + 1) }}
        </button>
      </p>
      <button
        @click="deleteFile(item.id)"
        class="btn rounded font-normal leading-4 ripple inline-block rounded-full flex items-center justify-center text-danger hover:bg-gray-200 dark:hover:bg-foreground"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
            clip-rule="evenodd"
          ></path>
        </svg>
      </button>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import fileDownload from "js-file-download";
import BaseAttachment from "../../../components/Base/BaseAttachments.vue";
import { useLoadingStore } from "../../../shared/store/loading";
import attachmentService from "../../../shared/api/services/attachment.service";
import { useAlertStore } from "../../../shared/store/alert";
import { useDialogStore } from "../../../shared/store/dialog";
import AttachmentModel from '../../../shared/api/models/AttachmentModel.js';
import { useAuthStore } from '../../../shared/store/auth'
import { useModalStore }   from "../../../shared/store/modal"
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'

const { t } = useI18n();

const loading = useLoadingStore();
const alert = useAlertStore();
const dialog = useDialogStore();
const auth   = useAuthStore();
const modal = useModalStore();

  const dataAttachments = ref([]);

onMounted(() => {
  getAttachments()
})

/****FUNCTIONS ****************** */

async function create(formData) {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await attachmentService.create(formData.value);
    formData.value.files = [];
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");
    getAttachments();
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

async function getAttachments() {
  loading.show();
  try {
    const response = await attachmentService.getByUser(auth.user_id);
    dataAttachments.value = response.data.data.map(item => new AttachmentModel(item));
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  }
  loading.hide();
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
    getAttachments();
    alert.show(t("mensages.delete"), "success");
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  }
  loading.hide();
}

async function openFile(path) {
  loading.show();
  try {
    const data = await attachmentService.downloadFile(path);
    const filename = path.split("/");
    fileDownload(data.data, filename["2"]);
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  }
  loading.hide();
}
</script>