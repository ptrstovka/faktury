<script lang="ts" setup>
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import { CalendarGridRow, type CalendarGridRowProps, useForwardProps } from 'reka-ui'
import { cn } from '@/Utils'

const props = defineProps<CalendarGridRowProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = reactiveOmit(props, 'class')

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <CalendarGridRow
    data-slot="calendar-grid-row"
    :class="cn('flex', props.class)" v-bind="forwardedProps"
  >
    <slot />
  </CalendarGridRow>
</template>
