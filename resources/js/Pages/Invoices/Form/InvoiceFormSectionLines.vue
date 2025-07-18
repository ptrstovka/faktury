<template>
  <div>
    <p class="font-bold text-lg mb-6">Položky</p>

    <FormControl v-if="form.lines.length === 0" :error="form.errors.lines">
      <div :class="cn('border border-input border-dashed rounded-md flex flex-col p-10 items-center justify-center', { 'border-destructive': !!form.errors.lines })">
        <template v-if="locked">
          <p class="text-sm font-medium">Táto faktúra neobsahuje žiadne položky.</p>
        </template>
        <template v-else>
          <p class="text-sm font-medium">Zatiaľ neboli pridané žiadne položky.</p>

          <Button class="mt-4" :icon="PlusIcon" @click="addLine" label="Pridať položku" />
        </template>
      </div>
    </FormControl>

    <template v-else>
      <FormControl :error="form.errors.lines">
        <InvoiceLineArrayInput
          v-model="form.lines"
          :separator="thousandsSeparator"
          :decimal="decimalSeparator"
          :quantity-precision="quantityPrecision"
          :price-precision="pricePrecision"
          :show-vat="form.vat_enabled"
          :errors="lineErrors"
          @clear-error="clearLineError($event[0], $event[1])"
          @removed="clearLineErrors"
          :disabled="locked"
          :class="{ 'border-destructive': !!form.errors.lines }"
        />
      </FormControl>

      <Button v-if="! locked" class="mt-4" size="sm" :icon="PlusIcon" @click="addLine" label="Ďalšia položka" />
    </template>
  </div>
</template>

<script setup lang="ts">
import { Button } from "@/Components/Button";
import { FormControl } from "@/Components/Form";
import { InvoiceLineArrayInput } from "@/Components/InvoiceLineInput";
import { injectInvoiceFormContext } from "@/Pages/Invoices/Form/index.ts";
import { cn } from "@/Utils";
import { PlusIcon } from "lucide-vue-next";

const {
  form, locked, addLine, lineErrors, clearLineErrors, clearLineError,
  thousandsSeparator, decimalSeparator, quantityPrecision, pricePrecision
} = injectInvoiceFormContext()
</script>
