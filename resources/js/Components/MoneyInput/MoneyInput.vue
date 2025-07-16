<template>
  <Input class="tabular-nums" @blur="onBlur" @focus="emit('focus')" @input="onInput" v-model="inputValue" />
</template>

<script setup lang="ts">
import { Input } from '@/Components/Input'
import { computed, nextTick, ref, watch } from "vue";
import { isValidNumber, decimalPlaces } from '@/Utils'
import currency from "currency.js";

const emit = defineEmits(['update:modelValue', 'input', 'blur', 'focus'])

const props = withDefaults(defineProps<{
  modelValue?: number | null
  precision?: number
  decimal?: string
  separator?: string
  symbol?: string
}>(), {
  precision: 2,
  decimal: ',',
  symbol: '€',
  separator: '',
})

const inputValue = ref<string>()
const inputMoney = ref<currency|null>(null)

const emitValue = (value: currency | null) => {
  emit('update:modelValue', value ? value.intValue : null)
}

const createMoney = (value: string | number, precision: number, symbol: string = '€') => {
  return currency(value, {
    precision: precision,
    symbol: symbol,
    decimal: '.',
    separator: '',
  })
}

const tryCreateMoneyFromMinor = (value: string | number | undefined | null, precision: number, symbol: string = '€') => {
  if (value === null || value === undefined) {
    return undefined
  }

  try {
    return currency(value, {
      errorOnInvalid: true,
      fromCents: true,
      precision: precision,
      symbol: symbol,
      decimal: '.',
      separator: '',
    })
  } catch (error) {
    return null
  }
}

const setValue = (money: currency | null) => {
  if (! money) {
    inputValue.value = undefined
    inputMoney.value = null
    return
  }

  inputValue.value = formatMoneyToInput(money)
  inputMoney.value = money
}

watch(computed(() => props.modelValue), value => {
  const opts = {
    pattern: '#',
    decimal: '.',
    separator: ''
  }

  const current = inputMoney.value?.format(opts)
  const updatedMoney = tryCreateMoneyFromMinor(value, props.precision, props.symbol)
  const updated = updatedMoney?.format(opts)

  if (current != updated) {
    setValue(updatedMoney || null)
  }
})

const onInput = () => {
  nextTick(() => {
    const input = inputValue.value

    if (input == '') {
      inputMoney.value = null
      emitValue(null)
    } else {
      const value = formatInputValue(inputValue.value)

      if (value) {
        inputMoney.value = value.money
        emitValue(value.money)
      }
    }

    emit('input')
  })
}

const onBlur = () => {
  emit('blur')

  const value = formatInputValue(inputValue.value)

  if (! value) {
    inputValue.value = undefined
    inputMoney.value = null

    emitValue(inputMoney.value)
    return
  }

  inputValue.value = value.formatted
  inputMoney.value = value.money

  emitValue(inputMoney.value)
}

const formatMoneyToInput = (money: currency) => {
  return money.format({
    pattern: '#',
    decimal: props.decimal,
    separator: props.separator,
  })
}

const formatInputValue = (value: string | null | undefined) => {
  if (value === null || value === undefined) {
    return null
  }

  let asNumber = `${value}`.trim().replace(/\s+/g, '').replace(/,/, '.').replace(/\.+$/, '')

  if (! isValidNumber(asNumber)) {
    return null
  }

  const number = Number(asNumber)

  const places = decimalPlaces(asNumber)

  if (places > props.precision) {
    asNumber = number.toFixed(props.precision)
  } else if (places < props.precision) {
    asNumber = `${asNumber}${places == 0 ? '.' : ''}${'0'.repeat(props.precision - places)}`
  }

  const money = createMoney(asNumber, props.precision, props.symbol)

  return {
    formatted: formatMoneyToInput(money),
    money,
  }
}

const money = tryCreateMoneyFromMinor(props.modelValue, props.precision, props.symbol)
if (money) {
  setValue(money)
}
</script>
