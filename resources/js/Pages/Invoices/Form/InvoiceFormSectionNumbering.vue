<template>
  <div class="grid grid-cols-2">
    <FormControl label="Číslo faktúry" :error="form.errors.public_invoice_number" hide-error>
      <FormInlineError>
        <Input
          autocomplete="off"
          v-model="form.public_invoice_number"
          :placeholder="draft ? 'automaticky' : undefined"
          @update:model-value="form.clearErrors('public_invoice_number')"
          :disabled="locked"
          class="disabled:opacity-100"
        />
      </FormInlineError>
    </FormControl>
  </div>
</template>

<script setup lang="ts">
import { FormControl, FormInlineError } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { injectInvoiceFormContext } from "@/Pages/Invoices/Form/index.ts";

const { form, locked, draft } = injectInvoiceFormContext()
</script>
