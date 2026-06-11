<script setup>
import { appConfig } from '../config/app'
</script>

<template>
  <footer class=" fixed bottom-0 w-full bg-white border-t px-6 py-3 flex justify-end items-center text-sm text-gray-600 space-x-2">
    <span>Developed by {{ appConfig.developer.name }}</span>
    <img
      :src="appConfig.developer.logo"
      :alt="appConfig.developer.name"
      class="h-5 w-5 object-contain"
    />
  </footer>
</template>