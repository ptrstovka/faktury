<template>
  <span
    data-slot="command-shortcut"
    :class="cn('text-muted-foreground ml-auto text-xs tracking-widest', props.class)"
  >
    <slot />
  </span>
</template>

<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/Utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>
