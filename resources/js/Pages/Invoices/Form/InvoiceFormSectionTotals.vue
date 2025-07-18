<template>
  <table v-if="showVat">
    <tbody>
    <template v-for="item in vatBreakdown">
      <tr class="text-sm">
        <td class="pb-0.5 text-right pr-4 text-muted-foreground">Základ DPH {{ item.rate }}%</td>
        <td class="tabular-nums pb-0.5 text-right font-medium">{{ formatMinorMoney(item.base) }}</td>
      </tr>

      <tr class="text-sm">
        <td class="pb-2 text-right pr-4 text-muted-foreground">DPH {{ item.rate }}%</td>
        <td class="tabular-nums pb-2 text-right font-medium">{{ formatMinorMoney(item.total) }}</td>
      </tr>
    </template>

    <tr class="border-t text-sm">
      <td class="pt-2 text-right pr-4 text-muted-foreground">Spolu bez DPH</td>
      <td class="tabular-nums pt-2 text-right font-medium">{{ formatMinorMoney(totalVatExclusive) }}</td>
    </tr>

    <tr class="text-sm">
      <td class="pb-2 text-right pr-4 text-muted-foreground">DPH</td>
      <td class="tabular-nums pb-2 text-right font-medium">{{ formatMinorMoney(totalVat) }}</td>
    </tr>

    <tr class="border-t">
      <td class="pt-2 text-right pr-4 font-bold text-lg">Spolu s DPH</td>
      <td class="tabular-nums pt-2 text-right font-bold text-lg">{{ formatMinorMoney(totalVatInclusive) }}</td>
    </tr>
    </tbody>
  </table>

  <table v-else>
    <tbody>
    <tr>
      <td class="text-right pr-4">Spolu</td>
      <td class="text-right">{{ formatMinorMoney(totalVatExclusive) }}</td>
    </tr>
    </tbody>
  </table>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { injectInvoiceFormContext } from ".";
import { createMoneyFromMinor } from '@/Utils'

const {
  vatBreakdown, form, totalVat, totalVatInclusive, totalVatExclusive,
  currency, pricePrecision, thousandsSeparator, decimalSeparator,
} = injectInvoiceFormContext()

const formatMinorMoney = computed(() => (value: number) => {
  const money = createMoneyFromMinor(value, pricePrecision.value, currency.value.symbol)

  return money.format({
    decimal: decimalSeparator.value,
    separator: thousandsSeparator.value,
    pattern: '# !',
  })
})

const showVat = computed(() => form.vat_enabled)
</script>
