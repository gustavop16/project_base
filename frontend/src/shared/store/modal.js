import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useModalStore = defineStore('modal', () => {
  const isOpen = ref(false)
  const component = ref(null)
  const props = ref({})
  const size = ref('medium')
  const title = ref('')
  const onSave = ref(null)
  const onEdit = ref(null)

  function open(comp, incomingProps = {}, incomingSize = 'medium', modalTitle = '', functionCallback = null) {
    component.value = comp
    props.value = incomingProps
    size.value = incomingSize
    title.value = modalTitle
    onSave.value = functionCallback
    onEdit.value = functionCallback
    isOpen.value = true
  }

  function close() {
    isOpen.value = false
    component.value = null
    props.value = {}
    title.value = ''
    size.value = 'medium'
    onSave.value = null
  }

  return { isOpen, component, props, size, title, onSave, onEdit, open, close }
})
