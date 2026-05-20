<script setup>
import Multiselect from '@vueform/multiselect'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    required: true // Ex: [{ label: 'Vue 3', value: 'vue' }]
  },
  label: String,
  placeholder: {
    type: String,
    default: 'Selecione...'
  },
  required: Boolean,
  error: String
})

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="w-full">
    <label v-if="label" class="block text-sm text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <Multiselect
      v-model="internalValue"
      mode="tags"
      :options="options"
      :placeholder="placeholder"
      class="text-xs"
      :class="error ? 'border-red-500' : ''"
      @update:modelValue="val => emit('update:modelValue', val)"
    />

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
