<template>
  <div class="gap-2 flex flex-col">
    <div class="flex flex-row gap-2">
      <FormControl :error="errors?.title" class="w-full" hide-error>
        <FormInlineError>
          <Input
            placeholder="Názov"
            v-model="title"
            @update:model-value="emit('clearError', 'title')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl class="w-24 shrink-0" :error="errors?.quantity" hide-error>
        <FormInlineError>
          <NumberInput
            :decimal="decimal"
            :separator="separator"
            placeholder="Počet"
            v-model="quantityInput"
            :max-precision="quantityPrecision"
            @input="onQuantityChanged"
            @blur="onBlur"
            @update:model-value="emit('clearError', 'quantity')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl class="w-16 shrink-0" :error="errors?.unit" hide-error>
        <FormInlineError>
          <Input
            placeholder="MJ"
            v-model="unit"
            @update:model-value="emit('clearError', 'unit')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl class="w-32 shrink-0" :error="errors?.unitPrice" hide-error>
        <FormInlineError>
          <MoneyInput
            :decimal="decimal"
            :separator="separator"
            :precision="pricePrecision"
            v-model="unitPriceVatExclInput"
            :placeholder="showVat ? 'Jed. cena bez DPH' : 'Jed. cena'"
            @input="onUnitPriceVatExclusiveChanged"
            @blur="onBlur"
            @update:model-value="emit('clearError', 'unitPrice')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl v-if="showVat" class="w-24 shrink-0" :error="errors?.vat" hide-error>
        <FormInlineError>
          <NumberInput
            :decimal="decimal"
            :separator="separator"
            placeholder="DPH %"
            v-model="vatRateInput"
            :max-precision="2"
            @input="onVatRateChanged"
            @blur="onBlur"
            @update:model-value="emit('clearError', 'vat')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl v-if="showVat" class="w-32 shrink-0" :error="errors?.totalVatInclusive" hide-error>
        <FormInlineError>
          <MoneyInput
            :decimal="decimal"
            :separator="separator"
            :precision="pricePrecision"
            placeholder="Spolu s DPH"
            v-model="totalVatInclusiveInput"
            @input="onTotalVatInclusiveChanged"
            @blur="onBlur"
            @update:model-value="emit('clearError', 'totalVatInclusive')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>

      <FormControl v-else class="w-32 shrink-0" :error="errors?.totalVatExclusive" hide-error>
        <FormInlineError>
          <MoneyInput
            :decimal="decimal"
            :separator="separator"
            :precision="pricePrecision"
            placeholder="Spolu"
            v-model="totalVatExclusiveInput"
            @input="onTotalVatExclusiveChanged"
            @blur="onBlur"
            @update:model-value="emit('clearError', 'totalVatExclusive')"
            :disabled="disabled"
            class="disabled:opacity-100"
          />
        </FormInlineError>
      </FormControl>
    </div>

    <FormControl :error="errors?.description" hide-error>
      <FormInlineError>
        <Input
          placeholder="Popis"
          v-model="description"
          @update:model-value="emit('clearError', 'description')"
          :disabled="disabled"
          class="disabled:opacity-100"
        />
      </FormInlineError>
    </FormControl>
  </div>
</template>

<script setup lang="ts">
import { FormInlineError, FormControl } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { createMoneyFromMinor } from "@/Utils";
import { nextTick, ref, watch } from "vue";
import { NumberInput } from '@/Components/NumberInput'
import { MoneyInput } from "@/Components/MoneyInput";
import { isEqual } from '.'

import type { InvoiceLine } from ".";

const emit = defineEmits(['update:modelValue', 'clearError'])

const props = withDefaults(defineProps<{
  modelValue: InvoiceLine
  pricePrecision?: number
  quantityPrecision?: number
  decimal?: string
  separator?: string
  showVat?: boolean
  errors?: Partial<Record<keyof InvoiceLine, string>>
  disabled?: boolean
}>(), {
  pricePrecision: 2,
  quantityPrecision: 4,
  decimal: ',',
  separator: '',
  showVat: true,
})

const title = ref<string>(props.modelValue.title)
const description = ref<string>(props.modelValue.description)
const unit = ref<string>(props.modelValue.unit)

const quantityInput = ref<number | null>(props.modelValue.quantity)
const unitPriceVatExclInput = ref<number | null>(props.modelValue.unitPrice)
const vatRateInput = ref<number | null>(props.modelValue.vat)
const totalVatExclusiveInput = ref<number | null>(props.modelValue.totalVatExclusive)
const totalVatInclusiveInput = ref<number | null>(props.modelValue.totalVatInclusive)

const createValue: () => InvoiceLine = () => ({
  title: title.value || '',
  description: description.value || '',
  quantity: quantityInput.value,
  unit: unit.value || '',
  unitPrice: unitPriceVatExclInput.value,
  vat: vatRateInput.value,
  totalVatExclusive: totalVatExclusiveInput.value,
  totalVatInclusive: totalVatInclusiveInput.value,
})

const asValidNumber: (value: any) => number | null = value => {
  if (value === null || value === undefined) {
    return null
  }

  const numericValue = Number(value)
  if (! isNaN(numericValue)) {
    return numericValue
  }

  return null
}

const isValidNumber: (value: number | null) => value is number = value => {
  return value !== null
}

const getQuantity = () => {
  const quantity = asValidNumber(quantityInput.value)

  if (quantity === null) {
    return 1
  }

  return quantity
}

const calculateTotals = () => {
  const unitPrice = asValidNumber(unitPriceVatExclInput.value)
  const quantity = getQuantity()

  if (! isValidNumber(unitPrice)) {
    return
  }

  if (quantity <= 0 || unitPrice <= 0) {
    return
  }

  // Recalculate total price without VAT
  const unitPriceMoney = createMoneyFromMinor(unitPrice, props.pricePrecision)
  const totalVatExclusiveMoney = unitPriceMoney.multiply(quantity)
  totalVatExclusiveInput.value = totalVatExclusiveMoney.intValue

  // Recalculate total price with VAT
  if (props.showVat) {
    const vat = asValidNumber(vatRateInput.value) as number

    if (! isValidNumber(vat)) {
      return;
    }

    totalVatInclusiveInput.value = totalVatExclusiveMoney.multiply(vat + 100).divide(100).intValue
  }
}

const calculateUnitPriceFromTaxExclusiveTotal = () => {
  const totalPrice = asValidNumber(totalVatExclusiveInput.value)
  const quantity = getQuantity()

  if (! isValidNumber(totalPrice)) {
    return
  }

  if (quantity <= 0 || totalPrice <= 0) {
    return;
  }

  const totalPriceMoney = createMoneyFromMinor(totalPrice, props.pricePrecision)
  unitPriceVatExclInput.value = totalPriceMoney.divide(quantity).intValue
}

const calculateUnitPriceFromTaxInclusiveTotal = () => {
  const totalPrice = asValidNumber(totalVatInclusiveInput.value)
  const quantity = getQuantity()
  const vat = asValidNumber(vatRateInput.value)

  if (! isValidNumber(totalPrice) || ! isValidNumber(vat)) {
    return
  }

  if (quantity < 0 || totalPrice <= 0) {
    return
  }

  const totalVatInclusivePriceMoney = createMoneyFromMinor(totalPrice, props.pricePrecision)
  totalVatExclusiveInput.value = totalVatInclusivePriceMoney.divide((100 + vat) / 100).intValue
  nextTick(() => calculateUnitPriceFromTaxExclusiveTotal())
}

watch(() => props.showVat, calculateVat => {
  if (calculateVat) {
    nextTick(() => calculateTotals())
  }
})

const onQuantityChanged = () => nextTick(() => calculateTotals())
const onVatRateChanged = () => nextTick(() => {
  calculateTotals()

  // Ak odstránim DPH a nemám ani cenu bez DPH tak odstránim aj cenu s DPH.
  if (! isValidNumber(asValidNumber(totalVatInclusiveInput.value))
    && ! isValidNumber(asValidNumber(vatRateInput.value))) {
    totalVatExclusiveInput.value = null
  }
})
const onUnitPriceVatExclusiveChanged = () => nextTick(() => calculateTotals())
const onTotalVatInclusiveChanged = () => nextTick(() => {
  calculateUnitPriceFromTaxInclusiveTotal()

  // Ak odstránim cenu s DPH tak odstránim aj cenu bez DPH.
  if (! isValidNumber(asValidNumber(totalVatInclusiveInput.value))) {
    totalVatExclusiveInput.value = null
  }
})
const onTotalVatExclusiveChanged = () => nextTick(() => {
  calculateUnitPriceFromTaxExclusiveTotal()
  calculateTotals()
})

const emitValueIfChanged = () => {
  const updatedValue = createValue()
  if (! isEqual(props.modelValue, updatedValue)) {
    emit('update:modelValue', updatedValue)
  }
}

watch([title, description, unit], () => emitValueIfChanged())

watch(() => props.modelValue, updatedModelValue => {
  const currentValue = createValue()
  if (! isEqual(currentValue, updatedModelValue)) {
    title.value = updatedModelValue.title
    description.value = updatedModelValue.description
    unit.value = unit.value

    quantityInput.value = updatedModelValue.quantity
    unitPriceVatExclInput.value = updatedModelValue.unitPrice
    vatRateInput.value = updatedModelValue.vat
    totalVatExclusiveInput.value = updatedModelValue.totalVatExclusive
    totalVatInclusiveInput.value = updatedModelValue.totalVatInclusive
  }
})

const onBlur = () => emitValueIfChanged()
</script>
