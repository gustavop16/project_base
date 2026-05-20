<script setup>
const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    required: true // [{ label: 'Sim', value: 'yes' }, ...]
  },
  name: String,
  label: String,
  error: String,
  required: Boolean
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div>
    <p v-if="label" class="mb-1 text-sm font-medium text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </p>

    <div class="space-y-1">
      <label
        v-for="option in options"
        :key="option.value"
        class="inline-flex items-center gap-2 text-xs text-gray-700"
      >
        <input
          type="radio"
          :name="name"
          :value="option.value"
          :checked="modelValue === option.value"
          @change="$emit('update:modelValue', option.value)"
          class="text-blue-600 focus:ring-blue-500"
        />
        {{ option.label }}
      </label>
    </div>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
