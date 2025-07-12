<template>
  <Label
    :class="cn(
      error && 'text-destructive',
      props.class,
    )"
    :for="props.for"
  >
    <slot />
  </Label>
</template>

<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'
import type { LabelProps } from 'reka-ui'
import { cn } from '@/lib/utils'
import { Label } from '@/components/ui/label'

const props = defineProps<LabelProps & {
  class?: HTMLAttributes['class']
  error?: string | null |undefined
}>()
</script>
