// src/shared/store/alert.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAlertStore = defineStore('alert', () => {
  const message = ref('')
  const type = ref('success') // valores: 'success', 'error', etc
  const visible = ref(false)

  function show(msg, alertType = 'success') {
    message.value = msg
    type.value = alertType
    visible.value = true

    setTimeout(() => {
      visible.value = false
    }, 3000)
  }

  return {
    message,
    type,
    visible,
    show
  }
})
