<template>
    
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
</template>


<script setup>

import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import fileDownload from "js-file-download";
import { useModalStore } from "../../shared/store/modal.js"
import { useLoadingStore } from "../../shared/store/loading.js";
import { useAlertStore } from "../../shared/store/alert.js";
import { useDialogStore } from "../../shared/store/dialog.js";
import userService from "../../shared/api/services/user.service.js";
import { useAuthStore } from '../../shared/store/auth.js';


const emit  = defineEmits(["onSave", "remove", "update:file"]);

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
  item_id   : Number,
  logo      : []
})

const formData = ref({
    item_id : props.item_id,
    model   : props.model,
    files   : [],
});

/* EVENTS ******************************/
onMounted(() => {
  
});

/* FUNCTIONS ******************************/
function closeModal(){
  modal.close();
}

function handleFileUpload(event) {
    const target = event.target;
    formData.value.files = target.files;
    const file = target.files[0]
    emit("update:file", file);

    /*if (target.files && target.files.length > 0) {
        formData.value.files = target.files;
        const file = target.files
        emit("update:file", file);

        console.log(formData.value.files)
    }*/
}
/*
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
*/
</script>