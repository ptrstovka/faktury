<template>
  <slot />

  <Toaster />
  <ConfirmationDialog />
</template>

<script setup lang="ts">
import { Toaster } from '@/Components/Sonner'
import { ConfirmationDialog } from '@/Components/ConfirmationDialog'
import 'vue-sonner/style.css'

defineOptions({
  inheritAttrs: false,
})
</script>
