<script setup>
import { computed } from 'vue'
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import IMask from 'imask'

const props = defineProps({
  modelValue: [String, Number],
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  labelSmall: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: String, // ex: "fas fa-user" (usando font awesome, heroicons, etc)
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  disabled: Boolean,
  required: Boolean,
  name: String,
  mask: {
    type: String,
    default: '' // exemplo: 'cpf', 'phone'
  }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const inputEl = ref(null)
let maskInstance = null

onMounted(() => {
  if (!props.mask || !inputEl.value) return

  let maskOptions = {}

  if (props.mask === 'cpf') {
    maskOptions = { mask: '000.000.000-00' }
  } 
  else if (props.mask === 'cnpj') {
    maskOptions = { mask: '00.000.000/0000-00' }
  } 
  else if (props.mask === 'phone') {
    maskOptions = {
      mask: [
        { mask: '(00) 0000-0000' },
        { mask: '(00) 00000-0000' }
      ]
    }
  }
  else if(props.mask === 'decimal'){
    maskOptions = [
        { mask: '0,00' },
        { mask: '00,00' },
        { mask: '000,00' }
    ]
  }

  maskInstance = IMask(inputEl.value, maskOptions)

  maskInstance.on('accept', () => {
    emit('update:modelValue', maskInstance.value)
  })
})

onBeforeUnmount(() => {
  if (maskInstance) {
    maskInstance.destroy()
  }
})

watch(() => props.modelValue, (val) => {
  if (maskInstance && maskInstance.value !== val) {
    maskInstance.value = val || ''
  }
})

const inputClasses = computed(() => [
  'w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-1',
  props.error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300',
  props.icon ? 'pl-10' : ''
])
</script>

<template>
  <div class="w-full">
    <label v-if="label" :for="name" class="block text-sm text-gray-700">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <span v-if="icon" class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
        <i :class="icon"></i>
      </span>

      <input
      ref="inputEl"
      :type="mask ? 'text' : type"
      :name="name"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :value="modelValue"
      @input="!mask && $emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur', $event)"
      class="w-full border rounded px-3 py-2 text-xs focus:outline-none focus:ring-1"
      :class="error ? 'border-red-500 focus:ring-red-300' : 'border-gray-300 focus:ring-blue-300'"
    />
    </div>
    <small v-if="labelSmall" class="text-xs text-blue-600">{{ labelSmall }}</small>
    <p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
  </div>
</template>
