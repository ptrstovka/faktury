<template>
  <div class="flex flex-col gap-6">
    <p class="font-bold text-lg">Platba</p>

    <FormControl label="Spôsob platby" :error="form.errors.payment_method" hide-error>
      <FormInlineError>
        <FormSelect :options="paymentMethods" v-model="form.payment_method" @update:model-value="form.clearErrors('payment_method')" :disabled="locked" class="disabled:opacity-100" />
      </FormInlineError>
    </FormControl>

    <template v-if="form.payment_method === 'bank-transfer'">
      <FormControl label="Názov banky" :error="form.errors.bank_name" hide-error>
        <FormInlineError>
          <Input autocomplete="off" v-model="form.bank_name" @update:model-value="form.clearErrors('bank_name')" :disabled="locked" class="disabled:opacity-100" />
        </FormInlineError>
      </FormControl>

      <FormControl label="Adresa banky" :error="form.errors.bank_address" hide-error>
        <FormInlineError>
          <Input autocomplete="off" v-model="form.bank_address" @update:model-value="form.clearErrors('bank_address')" :disabled="locked" class="disabled:opacity-100" />
        </FormInlineError>
      </FormControl>

      <FormControl label="BIC" :error="form.errors.bank_bic" hide-error>
        <FormInlineError>
          <Input autocomplete="off" v-model="form.bank_bic" @update:model-value="form.clearErrors('bank_bic')" :disabled="locked" class="disabled:opacity-100" />
        </FormInlineError>
      </FormControl>

      <FormControl label="Číslo účtu" :error="form.errors.bank_account_number" hide-error>
        <FormInlineError>
          <Input autocomplete="off" v-model="form.bank_account_number" @update:model-value="form.clearErrors('bank_account_number')" :disabled="locked" class="disabled:opacity-100" />
        </FormInlineError>
      </FormControl>

      <FormControl label="IBAN" :error="form.errors.bank_account_iban" hide-error>
        <FormInlineError>
          <Input autocomplete="off" v-model="form.bank_account_iban" @update:model-value="form.clearErrors('bank_account_iban')" :disabled="locked" class="disabled:opacity-100" />
        </FormInlineError>
      </FormControl>

      <div class="grid grid-cols-3 gap-4">
        <FormControl label="Variabilný symbol" :error="form.errors.variable_symbol" hide-error>
          <FormInlineError>
            <Input autocomplete="off" v-model="form.variable_symbol" :placeholder="draft ? 'automaticky' : undefined" @update:model-value="form.clearErrors('variable_symbol')" :disabled="locked" class="disabled:opacity-100" />
          </FormInlineError>
        </FormControl>

        <FormControl label="Špecifický symbol" :error="form.errors.specific_symbol" hide-error>
          <FormInlineError>
            <Input autocomplete="off" v-model="form.specific_symbol" @update:model-value="form.clearErrors('specific_symbol')" :disabled="locked" class="disabled:opacity-100" />
          </FormInlineError>
        </FormControl>

        <FormControl label="Konštantný symbol" :error="form.errors.constant_symbol" hide-error>
          <FormInlineError>
            <Input autocomplete="off" v-model="form.constant_symbol" @update:model-value="form.clearErrors('constant_symbol')" :disabled="locked" class="disabled:opacity-100" />
          </FormInlineError>
        </FormControl>
      </div>

      <FormControl :error="form.errors.show_pay_by_square" hide-error>
        <CheckboxControl
          v-model="form.show_pay_by_square"
          :error="form.errors.show_pay_by_square"
          @update:model-value="form.clearErrors('show_pay_by_square')"
          :disabled="locked"
        >Zobraziť QR kód Pay By Square</CheckboxControl>
      </FormControl>
    </template>
  </div>
</template>

<script setup lang="ts">
import { CheckboxControl } from "@/Components/Checkbox";
import { FormControl, FormInlineError, FormSelect } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { injectInvoiceFormContext } from "@/Pages/Invoices/Form/index.ts";

const { form, locked, paymentMethods, draft } = injectInvoiceFormContext()
</script>
