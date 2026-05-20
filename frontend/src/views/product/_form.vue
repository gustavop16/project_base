<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
        <BaseInput v-model="formData.name" :label="t('labels.name')" required/>
        <BaseInput v-model="formData.description" :label="t('labels.description')" required/>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <BaseSelect v-model="formData.unit" :label="t('labels.unit')" :options="optionsUnit" required/>
        <BaseInput v-model="formData.manufacturer" :label="t('labels.manufacturer')"/>
        <BaseInput v-model="formData.certification" :label="t('labels.certification')"/> 
    </div>
    <div class="grid grid-cols-3 gap-4">
        <BaseSelect v-model="formData.classification" :label="t('labels.classification')" :options="optionsClassification" />
        <BaseInput v-model="formData.technical_standard" :label="t('labels.technical_standard')"/>
        <BaseInput v-model="formData.sales_cost" :label="t('labels.sales_cost')" mask="decimal"/>
    </div>

    <BaseUpload :required="false" :multiple="false" v-model:file="formData.photo"></BaseUpload>

    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button
        class="flex items-center gap-1 text-blue-600 hover:underline"
        @click="onSave"
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
        class="flex items-center gap-1 text-blue-600 hover:underline"
        @click="closeModal"
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
</template>

<script setup>
import { ref, onMounted, watch, watchEffect } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput from "../../components/Base/BaseInput.vue";
import BaseDateInput from "../../components/Base/BaseDateInput.vue";
import BaseSelect from "../../components/Base/BaseSelect.vue";
import BaseCheckbox from "../../components/Base/BaseCheckbox.vue";
import BaseMultiSelectTags from "../../components/Base/BaseMultiSelectTags.vue";
import BaseUpload from "../../components/Base/BaseUpload.vue";
import productService from "../../shared/api/services/product.service";
import productModel from "../../shared/api/models/ProductModel"

import { useModalStore } from "../../shared/store/modal";
import { useAlertStore } from "../../shared/store/alert";
import { useLoadingStore } from "../../shared/store/loading";
import { getErrorMessage } from '../../shared/utils/getErrorMessage.js'

const { t } = useI18n();
const emit = defineEmits(["saved", "edited"]);
const props = defineProps(["formUpdate"]);
const modal = useModalStore();
const alert = useAlertStore();
const loading = useLoadingStore();

// Dados
const formData = ref({
    id: "",
    name: "",
    description: "",
    unit: "",
    manufacturer: "",
    certification: "",
    classification: "",
    technical_standard: "",
    sales_cost: "",
    photo: ""
}); 

const optionsUnit = ref([
    {label:'un' ,value:'un'},
    {label:'kg' ,value:'kg'},
    {label:'mg' ,value:'mg'},
    {label:'l' ,value:'l'},
    {label:'ml' ,value:'ml'}
]);

const optionsClassification = ref([
    {label:'Pesticida' ,value:'pesticide'},
    {label:'Inseticida' ,value:'insecticide'},
    {label:'Fungicida' ,value:'fungicide'},
    {label:'Sanitizante' ,value:'sanitizer'},
    {label:'Outros' ,value:'others'}
]);
 
/* EVENTS ******************************/

onMounted(() => {
    if (props.formUpdate?.id) {
        formData.value.id                = (!props.formUpdate.id) ? null : props.formUpdate.id;
        formData.value.name              = (!props.formUpdate.name) ? null : props.formUpdate.name;
        formData.value.description       = (!props.formUpdate.description) ? null : props.formUpdate.description;
        formData.value.unit              = (!props.formUpdate.unit) ? null : props.formUpdate.unit;
        formData.value.manufacturer      = (!props.formUpdate.manufacturer) ? null : props.formUpdate.manufacturer;
        formData.value.certification     = (!props.formUpdate.certification) ? null : props.formUpdate.certification;
        formData.value.classification    = (!props.formUpdate.classification) ? null : props.formUpdate.classification;
        formData.value.technical_standard= (!props.formUpdate.technical_standard) ? null : props.formUpdate.technical_standard;
        formData.value.sales_cost        = (!props.formUpdate.sales_cost) ? null : props.formUpdate.sales_cost;
    }
});

/* FUNCTIONS ******************************/
function closeModal(){
  modal.close();
}

async function submitForm() {
  if (formData.value.id) {
    edit();
  } else {
    create();
  }
}

async function create() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    await productService.create(formData.value);
    //MOSTRA NOTIFICAÇÃO SUCESSO
    alert.show(t("mensages.insert"), "success");
    //ENVIA RESPOSTA PARA ATULIZAR O DATATABLE
    emit("saved");
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

async function edit() {
  try {
    // ABRE LOADING
    loading.show();

    const input = new FormData();
    input.append("_method", "PUT")
    Object.entries(formData.value).forEach(([key, value]) => {
    if (value instanceof File || typeof value === "string" || typeof value === "number") {
        input.append(key, value);
    }
    });

    // REQUISIÇÃO AO ENDPONT
    await productService.update(formData.value.id,input);
    //NOTIFICATION SUCESSO
    alert.show(t("mensages.update"), "success");
    emit("edited");
    //FECHA MODAL
    modal.close();
  } catch (err) {
    //NOTIFICATION ERRO
    emit("edited");
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide();
  }
}


</script>

<style scoped>
</style>
