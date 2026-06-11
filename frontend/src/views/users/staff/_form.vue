<template>
  <form @submit.prevent="submitForm" class="space-y-2">
    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.name" :label="t('labels.name')" name="name" required />
      <BaseInput v-model="formData.email" label="Email" type="email" name="email" required />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <BaseInput v-model="formData.phone" :label="t('labels.phone')" name="phone" mask="phone" />
      <BaseSelect v-if="!props.selfEdit" v-model="formData.type" label="Tipo" :options="optionsType" required />
    </div>

    <!--
    <BaseMultiSelect
      v-if="!props.selfEdit && requiresVessel"
      v-model="formData.vessel_ids"
      :label="t('labels.vessels')"
      :options="vesselOptions"
    />
    -->

    <BaseTextarea v-model="formData.observations" label="Observações" name="observations" />

    <div class="border-t px-6 py-4 flex justify-end gap-4">
      <button class="flex items-center gap-1 text-blue-600 hover:underline" @click="onSave">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        Save
      </button>
      <button class="flex items-center gap-1 text-blue-600 hover:underline" @click="closeModal">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";

import BaseInput       from "../../../components/Base/BaseInput.vue";
import BaseSelect      from "../../../components/Base/BaseSelect.vue";
import BaseMultiSelect from "../../../components/Base/BaseMultiSelect.vue";
import BaseTextarea    from "../../../components/Base/BaseTextarea.vue";
import userService   from "../../../shared/api/services/user.service";
import vesselService from "../../../shared/api/services/vessel.service";
import { useModalStore }   from "../../../shared/store/modal";
import { useAlertStore }   from "../../../shared/store/alert";
import { useLoadingStore } from "../../../shared/store/loading";
import { getErrorMessage } from '../../../shared/utils/getErrorMessage.js'

const { t } = useI18n();
const emit = defineEmits(["saved", "edited"]);
const props = defineProps(["formUpdate", "user_id", "selfEdit"]);
const modal   = useModalStore();
const alert   = useAlertStore();
const loading = useLoadingStore();

const allVessels = ref([]);

const formData = ref({
  name:        "",
  email:       "",
  phone:       "",
  type:        "",
  vessel_ids:  [],
  observations: "",
});

const optionsType = ref([
  { label: "Admin",   value: "ADMIN"     },
  { label: "Armador", value: "SHIPOWNER" },
  { label: "Agente",  value: "AGENT"     },
]);

const TYPES_WITH_VESSEL = ['AGENT', 'SHIPOWNER'];
const requiresVessel = computed(() => TYPES_WITH_VESSEL.includes(formData.value.type));

const vesselOptions = computed(() =>
  allVessels.value.map(v => ({ label: v.name, value: v.id }))
);

onMounted(async () => {
  try {
    loading.show();
    const res = await vesselService.getAll();
    allVessels.value = res.data.data;
  } catch (err) {
    console.error('Erro ao carregar navios:', err);
  } finally {
    loading.hide();
  }

  if (props.formUpdate?.id) {
    formData.value.id           = props.formUpdate.id;
    formData.value.name         = props.formUpdate.name         ?? "";
    formData.value.email        = props.formUpdate.email        ?? "";
    formData.value.phone        = props.formUpdate.phone        ?? "";
    formData.value.type         = props.formUpdate.type         ?? "";
    formData.value.vessel_ids   = props.formUpdate.vessel_ids   ?? [];
    formData.value.observations = props.formUpdate.observations ?? "";
  }
});

function closeModal() {
  modal.close();
}

async function submitForm() {
  if (formData.value.id) {
    edit();
  } else {
    create();
  }
}

function buildPayload() {
  return {
    ...formData.value,
    vessel_ids: (formData.value.vessel_ids ?? []).map(Number),
  }
}

async function create() {
  try {
    loading.show();
    await userService.create(buildPayload());
    alert.show(t("mensages.insert"), "success");
    emit("saved");
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    loading.hide();
  }
}

async function edit() {
  try {
    loading.show();
    if (props.selfEdit) {
      await userService.updateProfile(buildPayload());
    } else {
      await userService.update(formData.value.id, { _method: "PUT", ...buildPayload() });
    }
    alert.show(t("mensages.update"), "success");
    emit("edited");
    modal.close();
  } catch (err) {
    alert.show(getErrorMessage(err.response), "error")
    console.error("Erro :", err);
  } finally {
    loading.hide();
  }
}
</script>

