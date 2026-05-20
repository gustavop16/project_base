<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    
      <BaseInput v-model="formData.current_password" 
      :label="t('labels.current_password')"
      type="password" 
      required
       />
      <BaseInput
        v-model="formData.new_password"
        :label="t('labels.new_password')"
        type="password"  
        required
      />
      <BaseInput
        v-model="formData.confirm_password"
        :label="t('labels.confirm_password')"
        type="password" 
        required
      />
   

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
import { ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput from "../../../components/Base/BaseInput.vue";
import BaseDateInput from "../../../components/Base/BaseDateInput.vue";
import BaseMultiSelect from "../../../components/Base/BaseMultiSelect.vue";
import BaseCheckbox from "../../../components/Base/BaseCheckbox.vue";
import BaseMultiSelectTags from "../../../components/Base/BaseMultiSelectTags.vue";
import BaseSelect from "../../../components/Base/BaseSelect.vue";
import userService from "../../../shared/api/services/user.service";
import basicDataService from "../../../shared/api/services/client.service.js";
import { useModalStore } from "../../../shared/store/modal.js";
import { useAlertStore } from "../../../shared/store/alert.js";
import { useLoadingStore } from "../../../shared/store/loading.js";
import { useAuthStore } from '../../../shared/store/auth.js'
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'

const { t } = useI18n();
const emit = defineEmits(["saved", "edited"]);
const props = defineProps(["formUpdate"]);
const modal = useModalStore();
const alert = useAlertStore();
const loading = useLoadingStore();
const auth   = useAuthStore();

const formData = ref({
  current_password: "",
  new_password: "",
  confirm_password: "",
}); 


/* EVENTS ******************************/

onMounted(() => {
  if (props.formUpdate?.id) {
    formData.value = props.formUpdate;
  }  
});

/* FUNCTIONS ******************************/

function closeModal(){
  modal.close();
}

async function submitForm() {
    edit();
}

async function edit() {
  try {
    // ABRE LOADING
    loading.show();
    // REQUISIÇÃO AO ENDPONT
    const response = await userService.updatePassword(
      auth.user_id,
      formData.value
    );
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


</script>

<style scoped>
</style>
