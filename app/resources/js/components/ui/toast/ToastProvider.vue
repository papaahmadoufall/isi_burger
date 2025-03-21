<script setup lang="ts">
import { ToastProvider } from 'reka-ui'
</script>

<template>
  <ToastProvider class="fixed top-4 right-4 bottom-auto left-auto">
    <slot />
  </ToastProvider>
</template>
