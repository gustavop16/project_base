<script setup>
const props = defineProps({
  modelValue: String,
  label: String,
  placeholder: String,
  rows: {
    type: Number,
    default: 4
  },
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

    <textarea
      :name="name"
      :rows="rows"
      :placeholder="placeholder"
      :required="required"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full border rounded px-3 py-2 text-xs focus:outline-none focus:ring-1"
      :class="error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300'"
    ></textarea>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
