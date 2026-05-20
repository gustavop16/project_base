<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="w-full max-w-md mx-auto">
      <label
        for="file"
        class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 cursor-pointer hover:border-blue-500 transition"
      >
        <div class="text-center">
          <p class="text-gray-700 text-sm">
            Clique aqui para selecionar o(s) arquivo(s)
          </p>
          <p class="text-xs text-gray-400 mt-1">
            Formatos: JPG, PNG (máx: 5MB)
          </p>
        </div>
        <input
          id="file"
          type="file"
          class="hidden"
          :multiple="multiple"
          :required="required"
          @change="handleFileUpload"
        />
      </label>
      <div v-if="formData.files.length > 0" class="mt-3 text-sm text-green-600">
        <strong>Arquivo(s) selecionado(s): </strong>
        <ul>
          <li v-for="(file, index) in formData.files" :key="index">
            {{ file.name }}
          </li>
        </ul>
      </div>
    </div>
    
    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        class="flex items-center gap-1 text-blue-600 hover:underline"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M5 13l4 4L19 7"
          />
        </svg>
        Save
      </button>

      <button
        @click="closeModal"
        class="flex items-center gap-1 text-blue-600 hover:underline"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-4 w-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        Cancel
      </button>
    </div>
  </form>

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
import { useModalStore } from "../../shared/store/modal"
import { useLoadingStore } from "../../shared/store/loading";
import { useAlertStore } from "../../shared/store/alert";
import { useDialogStore } from "../../shared/store/dialog";
import attachmentService from "../../shared/api/services/attachment.service";
import userService from "../../shared/api/services/user.service.js";
import AttachmentModel from '../../shared/api/models/AttachmentModel.js';
import { useAuthStore } from '../../shared/store/auth.js';


const emit  = defineEmits(["onSave", "remove"]);

const { t } = useI18n();
const modal = useModalStore();
const loading = useLoadingStore();
const alert = useAlertStore();
const dialog = useDialogStore();
const auth   = useAuthStore();

const dataAttachments = ref([]);

const props = defineProps({
  multiple:{
    type: Boolean,
    default: true,
  },
  required  : Boolean,
  model     : String,
  item_id   : Number
})

const formData = ref({
    item_id : props.item_id,
    model   : props.model,
    files   : [],
});

/* EVENTS ******************************/
onMounted(() => {
  if(props.model !== 'profiles'){
    getAttachments()
  }
});

/* FUNCTIONS ******************************/
function closeModal(){
  modal.close();
}

function handleFileUpload(event) {
    const target = event.target;
    if (target.files && target.files.length > 0) {
        formData.value.files = target.files;
    }
}

async function getAttachments() {
  loading.show();
  try {
    const response = await attachmentService.getAll(props.model ,props.item_id);
    dataAttachments.value = response.data.data.map(item => new AttachmentModel(item));
  } catch (err) {
    alert.show(t("mensages.error") + " " + err, "error");
    console.error("Erro :", err);
  }
  loading.hide();
}


function submitForm() {
  if(props.model === 'profiles'){
    updatePhoto();
  }else{
    create();
  } 
}

async function create() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await attachmentService.create(formData.value);
    formData.value.files = [];
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");
    //FECHA MODAL
    //modal.close();
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(t("mensages.error") + " " + err, "error");
    console.error("Erro :", err);
  } finally {
    getAttachments()
    // FINALIZA LOADING
    loading.hide();
  }
}

async function updatePhoto() {
  try {
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await userService.updatePhoto(props.item_id, formData.value);
   
    const user = response.data.data;
    var newPhoto = 'images/no-photo.png';
    if(user.photo){
      newPhoto = import.meta.env.VITE_APP_URL+'storage/profile/'+user.id+'/'+user.photo
    }
    localStorage.setItem('user_photo', newPhoto)
        
    formData.value.files = [];
    modal.close();
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");    
  } catch (err) {
    //NOTIFICATION ERRO
    alert.show(t("mensages.error") + " " + err, "error");
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    window.location.reload();
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
    alert.show(t("mensages.error") + " " + err, "error");
    console.error("Erro :", err);
  } finally {
    getAttachments()
    loading.hide();
  }
}

async function openFile(file) {
  loading.show();
  try {
    const path = `${props.model}/${props.item_id}/${file}`  
    const data = await attachmentService.downloadFile(path);
    const filename = path.split("/");
    fileDownload(data.data, filename["2"]);
  } catch (err) {
    alert.show(t("mensages.error") + " " + err, "error");
    console.error("Erro :", err);
  }
  loading.hide();
}

</script>