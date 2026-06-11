<script setup>
import { ref, computed } from "vue";

import BaseInput         from "./Base/BaseInput.vue";
import BaseSelect        from "./Base/BaseSelect.vue";
import BaseMultiSelect   from "./Base/BaseMultiSelect.vue";
import BaseRadio         from "./Base/BaseRadio.vue";
import BaseCheckboxGroup from "./Base/BaseCheckboxGroup.vue";

const props = defineProps({
  question: { type: Object, required: true },
});

const answer         = ref("");
const answerMultiple = ref([]);
const textInputs     = ref({});
const uploadedFiles  = ref([]);

const selectedTextOption = computed(() =>
  props.question.options?.find(o => o.has_text_input && o.value === answer.value) ?? null
);

const activeTextOptions = computed(() =>
  (props.question.options ?? []).filter(o => o.has_text_input && answerMultiple.value.includes(o.value))
);

function handleUpload(event) {
  uploadedFiles.value = Array.from(event.target.files).map(f => f.name);
}
</script>

<template>
  <div class="space-y-2">

    <template v-if="question.input_type === 'text'">
      <BaseInput v-model="answer" placeholder="Digite sua resposta..." />
    </template>

    <template v-else-if="question.input_type === 'number'">
      <BaseInput v-model="answer" type="number" placeholder="0" />
    </template>

    <template v-else-if="question.input_type === 'date'">
      <BaseInput v-model="answer" type="date" />
    </template>

    <template v-else-if="question.input_type === 'select_simple'">
      <BaseSelect v-model="answer" :options="question.options" />
      <BaseInput
        v-if="selectedTextOption"
        v-model="textInputs[selectedTextOption.value]"
        :label="selectedTextOption.label + ' — detalhes'"
        placeholder="Descreva..."
      />
    </template>

    <template v-else-if="question.input_type === 'select_multiple'">
      <BaseMultiSelect v-model="answerMultiple" :options="question.options" />
      <BaseInput
        v-for="opt in activeTextOptions"
        :key="opt.value"
        v-model="textInputs[opt.value]"
        :label="opt.label + ' — detalhes'"
        placeholder="Descreva..."
      />
    </template>

    <template v-else-if="question.input_type === 'radio'">
      <BaseRadio v-model="answer" :options="question.options" :name="'q_' + question.id" />
      <BaseInput
        v-if="selectedTextOption"
        v-model="textInputs[selectedTextOption.value]"
        :label="selectedTextOption.label + ' — detalhes'"
        placeholder="Descreva..."
      />
    </template>

    <template v-else-if="question.input_type === 'checkbox'">
      <BaseCheckboxGroup v-model="answerMultiple" :options="question.options" />
      <BaseInput
        v-for="opt in activeTextOptions"
        :key="opt.value"
        v-model="textInputs[opt.value]"
        :label="opt.label + ' — detalhes'"
        placeholder="Descreva..."
      />
    </template>

    <template v-else-if="question.input_type === 'upload'">
      <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
        </svg>
        <p class="text-sm text-gray-500">Clique para selecionar arquivo(s)</p>
        <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF (máx: 5 MB)</p>
        <input type="file" class="hidden" multiple @change="handleUpload" />
      </label>
      <ul v-if="uploadedFiles.length" class="text-xs text-green-600 space-y-1">
        <li v-for="f in uploadedFiles" :key="f">{{ f }}</li>
      </ul>
    </template>

  </div>
</template>
