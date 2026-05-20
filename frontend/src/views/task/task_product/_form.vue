<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
      <BaseSelect v-model="formData.product_id" :label="t('labels.product')" :options="optionsProduct" required/>
      <BaseInput v-model="formData.amount" :label="t('labels.quantity')" mask="decimal" required/>
    </div>

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
import { useRoute } from 'vue-router';
import BaseInput from "../../../components/Base/BaseInput.vue";
import BaseDateInput from "../../../components/Base/BaseDateInput.vue";
import { useModalStore } from '../../../shared/store/modal'
import taskProductService from "../../../shared/api/services/task_product.service.js";
import taskProductModel from '../../../shared/api/models/TaskProductModel';
import { useAlertStore } from "../../../shared/store/alert";
import { useLoadingStore } from "../../../shared/store/loading";
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'
import productService from "../../../shared/api/services/product.service.js";
import BaseSelect from "../../../components/Base/BaseSelect.vue";

const { t } = useI18n();
const emit  = defineEmits(["saved", "edited"]);
const props   = defineProps(["formUpdate"]);
const alert   = useAlertStore();
const loading = useLoadingStore();
const modal   = useModalStore();
const route   = useRoute();

const id_current = route.params.id;
const model      = route.meta.module;

// Dados
const formData = ref({
  task_id: route.params.id,
  product_id: "",
  amount: "",
}); 

const optionsProduct = ref([]);

/* EVENTS ******************************/
onMounted(() => {
    getProduct()
    if (props.formUpdate?.id) {
        formData.value.id           = (!props.formUpdate.id) ? null : props.formUpdate.id;
        formData.value.task_id      = (!props.formUpdate.task?.id) ? null : props.formUpdate.task.id;
        formData.value.product_id   = (!props.formUpdate.product?.id) ? null : props.formUpdate.product.id;
        formData.value.amount       = (!props.formUpdate.amount) ? null : props.formUpdate.amount;
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
    await taskProductService.create(formData.value);
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
    // REQUISIÇÃO AO ENDPONT
    await taskProductService.update(formData.value.id,formData.value);
    //NOTIFICATION SUCESSO
    alert.show(t("mensages.update"), "success");
    emit("edited");
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

async function getProduct() {
  try {
    // ABRE LOADING
    loading.show()
    // REQUISIÇÃO AO ENDPONT
    const response = await productService.getAll();
    optionsProduct.value = response.data.data.map(item => ({
      value: item.id,
      label: item.name
    }))    
  } catch (err) {
    //MOSTRA NOTIFICAÇÃO  ERRO
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    // FINALIZA LOADING
    loading.hide()
  }
}


</script>

<style scoped>
</style>
