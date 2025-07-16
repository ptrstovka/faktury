<template>
  <slot />

  <Toaster />
</template>

<script setup lang="ts">
import { Toaster } from '@/Components/Sonner'
import 'vue-sonner/style.css'

defineOptions({
  inheritAttrs: false,
})
</script>
