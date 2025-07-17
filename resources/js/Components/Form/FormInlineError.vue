<template>
  <div class="relative">
    <Primitive as-child :class="{ 'border-destructive dark:border-destructive': !!failure }">
      <slot />
    </Primitive>

    <TooltipProvider v-if="failure">
      <Tooltip>
        <TooltipTrigger class="absolute right-2.5 top-1/2 -translate-y-1/2 bg-background">
          <CircleAlertIcon class="size-4 text-destructive" />
        </TooltipTrigger>
        <TooltipContent class="text-destructive-foreground bg-destructive" hide-arrow>
          {{ failure }}
          <TooltipArrow class="bg-destructive fill-destructive z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]" />
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>

<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/Tooltip";
import { TooltipArrow, Primitive } from "reka-ui";
import { computed } from "vue";
import { injectFormControlContext } from ".";
import { CircleAlertIcon } from 'lucide-vue-next'

const props = defineProps<{
  error?: string | undefined | null
}>()

const context = injectFormControlContext()

const failure = computed(() => {
  if (props.error !== undefined) {
    return props.error
  }

  return context.value?.error
})
</script>
