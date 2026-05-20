<!-- src/views/Home.vue -->
<template>
  <div class="flex flex-col items-center justify-center">
    <h1>Bem-vindo ao Phoenix Clean</h1>
    <img id="logo" src="/images/logo/logo.png" alt="Logo" class="mb-2" />
  </div>
</template>

<script setup>
import { useModalStore } from './../shared/store/modal';
  const modal = useModalStore()

function openModal() {
  modal.open(true)
}

</script>

<style scoped>
.home {
  text-align: center;
  padding: 2rem;
}
#logo {
    position: relative;
    height: 100%;
    scale: 0.7;
    opacity: 0.1;
}
</style>
