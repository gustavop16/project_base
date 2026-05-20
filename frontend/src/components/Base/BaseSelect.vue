<script setup>
const props = defineProps({
  modelValue: [String, Number],
  label: String,
  options: {
    type: Array,
    required: true // [{ label: 'Brasil', value: 'BR' }]
  },
  placeholder: String,
  name: String,
  error: String,
  required: Boolean
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <label v-if="label" :for="name" class="block text-sm text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <select
      :name="name"
      :required="required"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="w-full border rounded px-3 py-2 text-xs focus:outline-none focus:ring-1"
      :class="error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300'"
    >
      <option disabled value="">{{ placeholder || 'Selecione' }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
