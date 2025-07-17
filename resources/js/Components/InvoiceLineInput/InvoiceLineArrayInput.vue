<template>
  <div v-if="lines.value.length > 0" class="border border-dashed border-input rounded-md overflow-hidden divide-y divide-dashed divide-input">
    <div v-for="(line, idx) in lines.value" class="flex py-3 even:bg-muted/30">
      <div class="w-10 shrink-0 flex flex-col items-center pt-2">
        <span class="font-bold">{{ idx + 1 }}</span>
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="mt-3.5 p-2 h-auto text-muted-foreground">
              <EllipsisVerticalIcon class="size-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="start">
            <DropdownMenuItem @select="remove(idx)">Odstrániť</DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>

      <InvoiceLineInput
        v-model="line.value"
        :price-precision="pricePrecision"
        :quantity-precision="quantityPrecision"
        :decimal="decimal"
        :separator="separator"
        :show-vat="showVat"
        :errors="errors ? errors[idx] : undefined"
        class="flex-1"
        @clear-error="emit('clearError', [$event, idx])"
      />

      <div class="w-10 shrink-0 flex flex-col items-center pt-6">
        <Button variant="ghost" class="p-2 h-auto text-muted-foreground cursor-move disabled:hover:bg-transparent" :disabled="lines.value.length <= 1">
          <GripVerticalIcon class="size-4" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Button } from "@/Components/Button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger
} from "@/Components/DropdownMenu";
import { reactive, watch } from "vue";
import type { InvoiceLine } from ".";
import { InvoiceLineInput, isEqual } from '.'
import { GripVerticalIcon, EllipsisVerticalIcon } from 'lucide-vue-next'

const emit = defineEmits(['update:modelValue', 'clearError', 'removed'])

const props = withDefaults(defineProps<{
  modelValue: Array<InvoiceLine>
  pricePrecision?: number
  quantityPrecision?: number
  decimal?: string
  separator?: string
  showVat?: boolean
  errors?: Array<Partial<Record<keyof InvoiceLine, string>>>
}>(), {
  pricePrecision: 2,
  quantityPrecision: 4,
  decimal: ',',
  separator: '',
  showVat: true,
})

interface InternalValue {
  value: InvoiceLine
}

const lines = reactive<{
  value: Array<InternalValue>
}>({
  value: props.modelValue.map(it => ({ value: { ...it } })),
})

const remove = (index: number) => {
  lines.value.splice(index, 1)
  emit('removed')
}

const areEqual = (a: Array<InvoiceLine>, b: Array<InvoiceLine>) => {
  if (a.length !== b.length) {
    return false
  }

  for (let i = 0; i < a.length; i++) {
    if (! isEqual(a[i], b[i])) {
      return false
    }
  }

  return true
}

watch(lines, updatedLines => {
  const modelValue = props.modelValue.map(it => ({ ...it }))
  const updated = updatedLines.value.map(it => ({ ...it.value }))
  if (! areEqual(modelValue, updated)) {
    emit('update:modelValue', updated)
  }
})

watch(() => props.modelValue, updatedModelValue => {
  const modelValue = updatedModelValue.map( it => ({ ...it }))
  const current = lines.value.map(it => ({ ...it.value }))
  if (! areEqual(modelValue, current)) {
    lines.value = modelValue.map(it => ({ value: it }))
  }
})
</script>
