<!-- src/components/Base/BaseAlert.vue -->
<template>
  <transition name="fade" appear>
    <div
      v-if="visible"
      :class="[
        'fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2',
        type === 'success' && 'bg-green-600',
        type === 'error' && 'bg-red-600',
        type === 'warning' && 'bg-yellow-500 text-black',
        type === 'info' && 'bg-blue-600'
      ]"
    >
      <slot>{{ message }}</slot>
      <button class="ml-4" @click="close">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watchEffect } from 'vue'
/*
const props = defineProps<{
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  duration: number
}>()*/

const props = defineProps({
  message: String,
  type: {
    type: String,
    validator: value =>
      ['success', 'error', 'warning', 'info'].includes(value),
  },
  duration: Number,
});


/*
const props = defineProps({
  message: string,
  type: 'success' | 'error' | 'warning' | 'info',
  duration: number
})*/

const visible = ref(true)

function close() {
  visible.value = false
}

watchEffect(() => {
  if (props.duration !== 0) {
    setTimeout(() => close(), props.duration ?? 3000)
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
