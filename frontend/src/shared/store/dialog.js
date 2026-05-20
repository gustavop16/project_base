// src/shared/store/dialog.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useDialogStore = defineStore('dialog', () => {
  const isOpen = ref(false)
  const title = ref('')
  const content = ref('')
  const onConfirm = ref(null)

  function openDialog({ dialogTitle, dialogContent, confirmCallback }) {
    title.value = dialogTitle
    content.value = dialogContent
    onConfirm.value = confirmCallback || null
    isOpen.value = true
  }

  function closeDialog() {
    isOpen.value = false
    title.value = ''
    content.value = ''
    onConfirm.value = null
  }

  function confirm() {
    if (onConfirm.value) onConfirm.value()
    closeDialog()
  }

  return {
    isOpen,
    title,
    content,
    openDialog,
    closeDialog,
    confirm,
  }
})
