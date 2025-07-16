<template>
  <Input class="tabular-nums" @blur="onBlur" @focus="emit('focus')" @input="onInput" v-model="inputValue" />
</template>

<script setup lang="ts">
import { Input } from '@/Components/Input'
import { computed, nextTick, ref, watch } from "vue";
import { decimalPlaces, isValidNumber } from "@/Utils";
import currency from "currency.js";

const inputValue = ref<string>()
const numberValue = ref<number | null>(null)

const emit = defineEmits(['update:modelValue', 'input', 'blur', 'focus'])

const props = withDefaults(defineProps<{
  modelValue?: number | null
  decimal?: string
  separator?: string
  minPrecision?: number
  maxPrecision?: number
}>(), {
  minPrecision: 0,
  maxPrecision: 2,
  decimal: ',',
  separator: '',
})

const setValue = (value: number | null | undefined) => {
  if (value === null || value === undefined) {
    inputValue.value = undefined
    numberValue.value = null
    return
  }

  const resolved = resolveValue(`${value}`)
  if (resolved) {
    inputValue.value = resolved.text
    numberValue.value = resolved.value
  } else {
    inputValue.value = undefined
    numberValue.value = null
  }
}

watch(computed(() => props.modelValue), updated => {
  if (updated !== numberValue.value) {
    setValue(updated)
  }
})

const emitValue = (value: number | null) => {
  emit('update:modelValue', value)
}

const onBlur = () => {
  emit('blur')

  const value = resolveValue(inputValue.value)

  if (! value) {
    inputValue.value = undefined
    numberValue.value = null

    emitValue(numberValue.value)
    return
  }

  inputValue.value = value.text
  numberValue.value = value.value

  emitValue(value.value)
}

const onInput = () => {
  nextTick(() => {
    const input = inputValue.value

    if (input == '') {
      numberValue.value = null
      emitValue(null)
    } else {
      const value = resolveValue(inputValue.value)

      if (value) {
        numberValue.value = value.value
        emitValue(value.value)
      }
    }

    emit('input')
  })
}

const resolveValue = (value: string | null | undefined) => {
  if (value === null || value === undefined) {
    return null
  }

  let asNumber = value.trim().replace(/\s+/g, '').replace(/,/, '.').replace(/\.+$/, '')

  if (! isValidNumber(asNumber)) {
    return null
  }

  const number = Number(asNumber)

  const places = decimalPlaces(asNumber)

  if (places > props.maxPrecision) {
    asNumber = number.toFixed(props.maxPrecision)
  } else if (places < props.minPrecision) {
    asNumber = `${asNumber}${places == 0 && props.minPrecision > 0 ? '.' : ''}${'0'.repeat(props.minPrecision - places)}`
  }

  const numericValue = Number(asNumber)

  // Using currency for formatting...
  const formatted = currency(asNumber, {
    decimal: '.',
    precision: decimalPlaces(asNumber),
    separator: '',
  }).format({
    pattern: '#',
    negativePattern: '-#',
    decimal: props.decimal,
    separator: props.separator,
    symbol: '',
  })

  return {
    value: numericValue,
    text: formatted,
  }
}

if (props.modelValue !== null && props.modelValue !== undefined) {
  setValue(props.modelValue)
}
</script>
