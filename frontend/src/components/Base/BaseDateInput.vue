<script setup>
const props = defineProps({
  modelValue: String,
  label: String,
  name: String,
  placeholder: String,
  error: String,
  required: Boolean,
  disabled: Boolean,
  labelSmall: {
      type: String,
      default: ''
  },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <label v-if="label" :for="name" class="block text-sm text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <input
      type="date"
      :id="name"
      :name="name"
      :required="required"
      :disabled="disabled"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      class="w-full border rounded px-3 py-2 text-xs focus:outline-none focus:ring-1"
      :class="error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300'"
    />
    <small v-if="labelSmall" class="text-xs text-blue-600">{{ labelSmall }}</small>
    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
