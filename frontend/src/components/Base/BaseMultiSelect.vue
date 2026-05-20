<script setup>
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    required: true // Ex: [{ label: 'Brasil', value: 'BR' }, ...]
  },
  label: String,
  name: String,
  placeholder: String,
  error: String,
  required: Boolean
})

const emit = defineEmits(['update:modelValue'])

function handleChange(event) {
  const selectedOptions = Array.from(event.target.selectedOptions).map(opt => opt.value)
  emit('update:modelValue', selectedOptions)
}
</script>

<template>
  <div>
    <label v-if="label" :for="name" class="block text-sm font-medium text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <select
      multiple
      :name="name"
      :required="required"
      @change="handleChange"
      class="w-full border rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 min-h-[120px]"
      :class="error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300'"
    >
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
        :selected="modelValue.includes(option.value)"
      >
        {{ option.label }}
      </option>
    </select>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
