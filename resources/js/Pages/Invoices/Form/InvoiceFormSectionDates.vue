<template>
  <div class="grid grid-cols-3 gap-4">
    <FormControl label="Dátum vystavenia" :error="form.errors.issued_at" hide-error>
      <FormInlineError>
        <DatePicker
          v-model="form.issued_at"
          @update:model-value="form.clearErrors('issued_at')"
          close-on-select
          :disabled="locked"
          class="disabled:opacity-100"
        />
      </FormInlineError>
    </FormControl>

    <FormControl label="Dátum dodania" :error="form.errors.supplied_at" hide-error>
      <FormInlineError>
        <DatePicker
          v-model="form.supplied_at"
          @update:model-value="form.clearErrors('supplied_at')"
          close-on-select
          :disabled="locked"
          class="disabled:opacity-100"
        />
      </FormInlineError>
    </FormControl>

    <FormControl label="Dátum splatnosti" :error="form.errors.payment_due_to" hide-error>
      <FormInlineError>
        <DatePicker
          v-model="form.payment_due_to"
          @update:model-value="form.clearErrors('payment_due_to')"
          close-on-select
          :disabled="locked"
          class="disabled:opacity-100"
        />
      </FormInlineError>
    </FormControl>
  </div>
</template>

<script setup lang="ts">
import { DatePicker } from "@/Components/DatePicker";
import { FormControl, FormInlineError } from "@/Components/Form";
import { injectInvoiceFormContext } from "@/Pages/Invoices/Form/index.ts";

const { form, locked } = injectInvoiceFormContext()
</script>
