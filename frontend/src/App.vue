<template>
  <router-view />
  <base-modal/>
  <base-dialog/>
  <BaseLoading v-if="loading.isLoading" />
  <BaseAlert
    v-if="alert.visible"
    :message="alert.message"
    :type="alert.type"
  />
</template>

<script setup>
import { useLoadingStore } from './shared/store/loading'
import { useAlertStore } from './shared/store/alert'
import BaseAlert from './components/Base/BaseAlert.vue'
import BaseModal from './components/Base/BaseModal.vue'
import BaseLoading from './components/Base/BaseLoading.vue'
import BaseDialog from './components/Base/BaseDialog.vue'

const loading = useLoadingStore()
const alert = useAlertStore()
</script>

