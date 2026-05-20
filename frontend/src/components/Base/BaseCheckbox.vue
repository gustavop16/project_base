<script setup>
const props = defineProps({
  modelValue: Boolean,
  label: String,
  name: String,
  error: String,
  required: Boolean
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="flex items-start gap-2">
    <input
      type="checkbox"
      :name="name"
      :checked="modelValue"
      :required="required"
      @change="$emit('update:modelValue', $event.target.checked)"
      class="mt-1 border-gray-300 focus:ring-blue-500 text-blue-600"
    />
    <div>
      <label class="text-sm text-gray-700">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
    </div>
  </div>
</template>
