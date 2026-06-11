<script setup>
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    required: true // [{ label: 'Sim', value: 'yes', has_text_input: false }]
  },
  name:     String,
  label:    String,
  error:    String,
  required: Boolean,
})

const emit = defineEmits(['update:modelValue'])

function toggle(value) {
  const next = [...props.modelValue]
  const idx  = next.indexOf(value)
  idx === -1 ? next.push(value) : next.splice(idx, 1)
  emit('update:modelValue', next)
}
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
        class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer select-none"
      >
        <input
          type="checkbox"
          :name="name"
          :value="option.value"
          :checked="modelValue.includes(option.value)"
          @change="toggle(option.value)"
          class="accent-blue-600"
        />
        {{ option.label }}
      </label>
    </div>

    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
