<template>
  <div :class="cn('flex items-center space-x-2 relative w-fit pr-6', $attrs.class || '')">
    <Checkbox
      v-model="checked"
      :value="value"
      :indeterminate="indeterminate"
      :id="id"
      :disabled="disabled"
      :class="{ 'checked:bg-destructive border-destructive': !!error }"
    />

    <Label :for="id" :class="{ 'text-destructive': !!error }">
      <slot />
    </Label>

    <TooltipProvider v-if="error">
      <Tooltip>
        <TooltipTrigger class="absolute right-2.5 top-1/2 -translate-y-1/2 bg-background">
          <CircleAlertIcon class="size-4 text-destructive" />
        </TooltipTrigger>
        <TooltipContent class="text-destructive-foreground bg-destructive" hide-arrow>
          {{ error }}
          <TooltipArrow class="bg-destructive fill-destructive z-50 size-2.5 translate-y-[calc(-50%_-_2px)] rotate-45 rounded-[2px]" />
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>

<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/Tooltip";
import { useVModel } from "@vueuse/core";
import { cn } from "@/Utils";
import { Label } from "@/Components/Label";
import { CircleAlertIcon } from "lucide-vue-next";
import { Checkbox } from ".";
import { TooltipArrow, useId } from "reka-ui";

const emit = defineEmits(['update:modelValue'])

const props = defineProps<{
  modelValue?: any
  value?: any
  indeterminate?: boolean
  error?: string | null | undefined
  disabled?: boolean
}>()

const checked = useVModel(props, 'modelValue', emit)

const id = useId()
</script>
