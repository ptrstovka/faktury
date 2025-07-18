<template>
  <div class="flex flex-col gap-6">
    <p class="font-bold text-lg">Nastavenia dokladu</p>

    <FormControl label="DPH" :error="form.errors.vat_enabled || form.errors.vat_reverse_charge" hide-error>
      <div class="flex flex-col gap-2">
        <CheckboxControl
          v-model="form.vat_enabled"
          :error="form.errors.vat_enabled"
          @update:model-value="form.clearErrors('vat_enabled')"
          :disabled="locked"
        >Zapnúť DPH</CheckboxControl>

        <CheckboxControl
          v-model="form.vat_reverse_charge"
          :error="form.errors.vat_reverse_charge"
          @update:model-value="form.clearErrors('vat_reverse_charge')"
          :disabled="locked"
        >Prenesenie daňovej povinnosti</CheckboxControl>
      </div>
    </FormControl>

    <FormControl label="Vystavil" :error="form.errors.issued_by || form.errors.issued_by_email || form.errors.issued_by_website || form.errors.issued_by_phone_number" hide-error>
      <div class="flex flex-col gap-2">
        <FormInlineError :error="form.errors.issued_by || null">
          <Input
            placeholder="Meno a priezvisko"
            v-model="form.issued_by"
            @update:model-value="form.clearErrors('issued_by')"
            :disabled="locked"
            class="disabled:opacity-100"
            autocomplete="off"
          />
        </FormInlineError>

        <FormInlineError :error="form.errors.issued_by_phone_number || null">
          <Input
            placeholder="Tel. číslo"
            v-model="form.issued_by_phone_number"
            @update:model-value="form.clearErrors('issued_by_phone_number')"
            :disabled="locked"
            class="disabled:opacity-100"
            autocomplete="off"
          />
        </FormInlineError>

        <FormInlineError :error="form.errors.issued_by_email || null">
          <Input
            placeholder="E-Mail"
            v-model="form.issued_by_email"
            @update:model-value="form.clearErrors('issued_by_email')"
            :disabled="locked"
            class="disabled:opacity-100"
            autocomplete="off"
          />
        </FormInlineError>

        <FormInlineError :error="form.errors.issued_by_website || null">
          <Input
            placeholder="Webová stránka"
            v-model="form.issued_by_website"
            @update:model-value="form.clearErrors('issued_by_website')"
            :disabled="locked"
            class="disabled:opacity-100"
            autocomplete="off"
          />
        </FormInlineError>
      </div>
    </FormControl>

    <FormControl label="Šablóna" :error="form.errors.template" hide-error>
      <FormInlineError>
        <FormSelect :options="templates" v-model="form.template" @update:model-value="form.clearErrors('template')" :disabled="locked" class="disabled:opacity-100" />
      </FormInlineError>
    </FormControl>

    <FormControl
      label="Poznámka v pätičke"
      :error="form.errors.footer_note"
      help="Tu môžete uviesť poznámku o zápise spoločnosti v obchodnom registri."
      hide-error
    >
      <FormInlineError>
        <Textarea autocomplete="off" v-model="form.footer_note" @update:model-value="form.clearErrors('footer_note')" :disabled="locked" class="disabled:opacity-100" />
      </FormInlineError>
    </FormControl>

    <div class="grid grid-cols-2 gap-4">
      <!-- TODO: pridať možnosť nastaviť si custom logo priamo na fakture -->
      <FormControl label="Logo">
        <div class="border border-dashed rounded-md border-input flex items-center justify-center p-2">
          <img class="h-20 object-contain object-center" v-if="invoice.logoUrl" :src="invoice.logoUrl" alt="">
        </div>
      </FormControl>

      <!-- TODO: pridať možnosť nastaviť si custom podpis priamo na fakture -->
      <FormControl label="Podpis">
        <div class="border border-dashed rounded-md border-input flex items-center justify-center p-2">
          <img class="h-20 object-contain object-center" v-if="invoice.signatureUrl" :src="invoice.signatureUrl" alt="">
        </div>
      </FormControl>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CheckboxControl } from "@/Components/Checkbox";
import { FormControl, FormInlineError, FormSelect } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { Textarea } from "@/Components/Textarea";
import { injectInvoiceFormContext } from "@/Pages/Invoices/Form/index.ts";

const { form, locked, templates, invoice } = injectInvoiceFormContext()
</script>
