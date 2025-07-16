<template>
  <Head title="Faktúra" />

  <AppLayout class="pb-16">

    <div class="flex flex-row justify-between items-center border-b py-4 mb-4">
      <div class="">
        <p class="text-2xl font-medium text-muted-foreground">Nová faktúra</p>
      </div>

      <div class="flex gap-2">
        <Button @click="save" variant="ghost" size="sm">Zahodiť koncept</Button>
        <Button :processing="form.processing" @click="save" variant="outline" size="sm">Uložiť</Button>
        <Button @click="save" size="sm">Vystaviť</Button>
      </div>
    </div>

    <div class="space-y-12">
      <div class="grid grid-cols-2 gap-12">
        <div class="grid grid-cols-2">
          <FormControl label="Číslo faktúry" :error="form.errors.public_invoice_number">
            <Input v-model="form.public_invoice_number" placeholder="automaticky" />
          </FormControl>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <FormControl label="Dátum vystavenia" :error="form.errors.issued_at">
            <DatePicker v-model="form.issued_at" />
          </FormControl>

          <FormControl label="Dátum dodania" :error="form.errors.supplied_at">
            <DatePicker v-model="form.supplied_at" />
          </FormControl>

          <FormControl label="Dátum splatnosti" :error="form.errors.payment_due_to">
            <DatePicker v-model="form.payment_due_to" />
          </FormControl>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-12">
        <div class="flex flex-col gap-6">
          <p class="font-bold text-lg">Dodávateľ</p>

          <FormControl label="Obchodné meno" :error="form.errors.supplier_business_name">
            <Input v-model="form.supplier_business_name" />
          </FormControl>

          <FormControl label="Adresa" :error="form.errors.supplier_address_line_one || form.errors.supplier_address_line_two || form.errors.supplier_address_line_three">
            <div class="flex flex-col gap-2">
              <Input v-model="form.supplier_address_line_one" />
              <Input v-model="form.supplier_address_line_two" />
              <Input v-model="form.supplier_address_line_three" />
            </div>
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="PSČ" :error="form.errors.supplier_address_postal_code">
              <Input v-model="form.supplier_address_postal_code" />
            </FormControl>

            <FormControl label="Mesto" :error="form.errors.supplier_address_city" class="col-span-2">
              <Input v-model="form.supplier_address_city" />
            </FormControl>
          </div>

          <FormControl label="Krajina" :error="form.errors.supplier_address_country">
            <FormSelect :options="countries" v-model="form.supplier_address_country" />
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="IČO" :error="form.errors.supplier_business_id">
              <Input v-model="form.supplier_business_id" />
            </FormControl>

            <FormControl label="DIČ" :error="form.errors.supplier_vat_id">
              <Input v-model="form.supplier_vat_id" />
            </FormControl>

            <FormControl label="IČDPH" :error="form.errors.supplier_eu_vat_id">
              <Input v-model="form.supplier_eu_vat_id" />
            </FormControl>
          </div>

          <FormControl
            label="Doplňujúce informácie"
            :error="form.errors.supplier_additional_info"
            help="Vyplňte ak potrebujete uviesť ďalšie informácie k dodávateľovi."
          >
            <Textarea v-model="form.supplier_additional_info" />
          </FormControl>

          <div class="grid grid-cols-2 gap-4">
            <FormControl label="E-Mail" :error="form.errors.supplier_email">
              <Input v-model="form.supplier_email" />
            </FormControl>

            <FormControl label="Tel. číslo" :error="form.errors.supplier_phone_number">
              <Input v-model="form.supplier_phone_number" />
            </FormControl>
          </div>

          <FormControl label="Webová stránka" :error="form.errors.supplier_website">
            <Input v-model="form.supplier_website" />
          </FormControl>
        </div>

        <div class="flex flex-col gap-6">
          <p class="font-bold text-lg">Odberateľ</p>

          <FormControl label="Obchodné meno" :error="form.errors.customer_business_name">
            <Input v-model="form.customer_business_name" />
          </FormControl>

          <FormControl label="Adresa" :error="form.errors.customer_address_line_one || form.errors.customer_address_line_two || form.errors.customer_address_line_three">
            <div class="flex flex-col gap-2">
              <Input v-model="form.customer_address_line_one" />
              <Input v-model="form.customer_address_line_two" />
              <Input v-model="form.customer_address_line_three" />
            </div>
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="PSČ" :error="form.errors.customer_address_postal_code">
              <Input v-model="form.customer_address_postal_code" />
            </FormControl>

            <FormControl label="Mesto" :error="form.errors.customer_address_city" class="col-span-2">
              <Input v-model="form.customer_address_city" />
            </FormControl>
          </div>

          <FormControl label="Krajina" :error="form.errors.customer_address_country">
            <FormSelect :options="countries" v-model="form.customer_address_country" />
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="IČO" :error="form.errors.customer_business_id">
              <Input v-model="form.customer_business_id" />
            </FormControl>

            <FormControl label="DIČ" :error="form.errors.customer_vat_id">
              <Input v-model="form.customer_vat_id" />
            </FormControl>

            <FormControl label="IČDPH" :error="form.errors.customer_eu_vat_id">
              <Input v-model="form.customer_eu_vat_id" />
            </FormControl>
          </div>

          <FormControl
            label="Doplňujúce informácie"
            :error="form.errors.customer_additional_info"
            help="Vyplňte ak potrebujete uviesť ďalšie informácie k odberateľovi."
          >
            <Textarea v-model="form.customer_additional_info" />
          </FormControl>

          <div class="grid grid-cols-2 gap-4">
            <FormControl label="E-Mail" :error="form.errors.customer_email">
              <Input v-model="form.customer_email" />
            </FormControl>

            <FormControl label="Tel. číslo" :error="form.errors.customer_phone_number">
              <Input v-model="form.customer_phone_number" />
            </FormControl>
          </div>
        </div>
      </div>

      <div>
        <p class="font-bold text-lg">Položky</p>

        <div v-if="form.lines.length === 0" class="border border-input border-dashed rounded-md flex flex-col p-10 items-center justify-center">
          <p class="text-sm font-medium">Zatiaľ neboli pridané žiadne položky.</p>

          <Button class="mt-4" :icon="PlusIcon" @click="addLine" label="Pridať položku" />
        </div>

        <template v-else>
          <InvoiceLineArrayInput
            v-model="form.lines"
            :separator="thousandsSeparator"
            :decimal="decimalSeparator"
            :quantity-precision="quantityPrecision"
            :price-precision="pricePrecision"
            :show-vat="form.vat_enabled"
          />

          <Button class="mt-4" :icon="PlusIcon" @click="addLine" label="Ďalšia položka" />
        </template>
      </div>

      <div class="grid grid-cols-2 gap-12">
        <div class="flex flex-col gap-6">
          <p class="font-bold text-lg">Nastavenia dokladu</p>

          <FormControl label="DPH" :error="form.errors.vat_enabled || form.errors.vat_reverse_charge">
            <div class="flex flex-col gap-2">
              <CheckboxControl v-model="form.vat_enabled">Zapnúť DPH</CheckboxControl>
              <CheckboxControl v-if="form.vat_enabled" v-model="form.vat_reverse_charge">Prenesenie daňovej povinnosti</CheckboxControl>
            </div>
          </FormControl>

          <FormControl label="Vystavil" :error="form.errors.issued_by || form.errors.issued_by_email || form.errors.issued_by_website || form.errors.issued_by_phone_number">
            <div class="flex flex-col gap-2">
              <Input placeholder="Meno a priezvisko" v-model="form.issued_by" />
              <Input placeholder="Tel. číslo" v-model="form.issued_by_phone_number" />
              <Input placeholder="E-Mail" v-model="form.issued_by_email" />
              <Input placeholder="Webová stránka" v-model="form.issued_by_website" />
            </div>
          </FormControl>

          <FormControl label="Šablóna" :error="form.errors.template">
            <FormSelect :options="templates" v-model="form.template" />
          </FormControl>

          <FormControl
            label="Poznámka v pätičke"
            :error="form.errors.footer_note"
            help="Tu môžete uviesť poznámku o zápise spoločnosti v obchodnom registri."
          >
            <Textarea v-model="form.footer_note" />
          </FormControl>
        </div>

        <div class="flex flex-col gap-6">
          <p class="font-bold text-lg">Platba</p>

          <FormControl label="Spôsob platby" :error="form.errors.payment_due_to">
            <FormSelect :options="paymentMethods" v-model="form.payment_method" />
          </FormControl>

          <template v-if="form.payment_method === 'bank-transfer'">
            <FormControl label="Názov banky" :error="form.errors.bank_name">
              <Input v-model="form.bank_name" />
            </FormControl>

            <FormControl label="Adresa banky" :error="form.errors.bank_address">
              <Input v-model="form.bank_address" />
            </FormControl>

            <FormControl label="BIC" :error="form.errors.bank_bic">
              <Input v-model="form.bank_bic" />
            </FormControl>

            <FormControl label="Číslo účtu" :error="form.errors.bank_account_number">
              <Input v-model="form.bank_account_number" />
            </FormControl>

            <FormControl label="IBAN" :error="form.errors.bank_account_iban">
              <Input v-model="form.bank_account_iban" />
            </FormControl>

            <div class="grid grid-cols-3 gap-4">
              <FormControl label="Variabilný symbol" :error="form.errors.variable_symbol">
                <Input v-model="form.variable_symbol" placeholder="automaticky" />
              </FormControl>

              <FormControl label="Špecifický symbol" :error="form.errors.specific_symbol">
                <Input v-model="form.specific_symbol" />
              </FormControl>

              <FormControl label="Konštantný symbol" :error="form.errors.constant_symbol">
                <Input v-model="form.constant_symbol" />
              </FormControl>
            </div>

            <FormControl :error="form.errors.show_pay_by_square">
              <CheckboxControl v-model="form.show_pay_by_square">Zobraziť QR kód Pay By Square</CheckboxControl>
            </FormControl>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Button } from "@/Components/Button";
import { CheckboxControl } from "@/Components/Checkbox";
import { DatePicker } from "@/Components/DatePicker";
import { FormControl, FormSelect } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { Textarea } from "@/Components/Textarea";
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import type { SelectOption } from "@stacktrace/ui";
import { createInvoiceLine, InvoiceLineArrayInput } from '@/Components/InvoiceLineInput'
import { useMagicKeys } from "@vueuse/core";
import { PlusIcon } from "lucide-vue-next";
import { watch } from "vue";
import { toast } from "vue-sonner";

interface Company {
  businessName: string | null
  businessId: string | null
  vatId: string | null
  euVatId: string | null
  email: string | null
  phoneNumber: string | null
  website: string | null
  additionalInfo: string | null
  addressLineOne: string | null
  addressLineTwo: string | null
  addressLineThree: string | null
  addressCity: string | null
  addressPostalCode: string | null
  addressCountry: string | null
}

const props = defineProps<{
  id: string
  publicInvoiceNumber: string | null
  supplier: Company
  customer: Company
  bankName: string | null
  bankAddress: string | null
  bankBic: string | null
  bankAccountNumber: string | null
  bankAccountIban: string | null
  issuedAt: string | null
  suppliedAt: string | null
  paymentDueTo: string | null
  vatEnabled: boolean
  locale: string
  template: string
  footerNote: string | null
  issuedBy: string | null
  issuedByEmail: string | null
  issuedByPhoneNumber: string | null
  issuedByWebsite: string | null
  paymentMethod: string
  variableSymbol: string | null
  specificSymbol: string | null
  constantSymbol: string | null
  showPayBySquare: boolean
  vatReverseCharge: boolean

  countries: Array<SelectOption>
  templates: Array<SelectOption>
  paymentMethods: Array<SelectOption<'cash' | 'bank-transfer'>>

  thousandsSeparator: string
  decimalSeparator: string
  quantityPrecision: number
  pricePrecision: number
}>()

const form = useForm(() => ({
  issued_at: props.issuedAt || '',
  supplied_at: props.suppliedAt || '',
  payment_due_to: props.paymentDueTo || '',
  public_invoice_number: props.publicInvoiceNumber || '',

  supplier_business_name: props.supplier.businessName || '',
  supplier_business_id: props.supplier.businessId || '',
  supplier_vat_id: props.supplier.vatId || '',
  supplier_eu_vat_id: props.supplier.euVatId || '',
  supplier_email: props.supplier.email || '',
  supplier_phone_number: props.supplier.phoneNumber || '',
  supplier_website: props.supplier.website || '',
  supplier_additional_info: props.supplier.additionalInfo || '',
  supplier_address_line_one: props.supplier.addressLineOne || '',
  supplier_address_line_two: props.supplier.addressLineTwo || '',
  supplier_address_line_three: props.supplier.addressLineThree || '',
  supplier_address_city: props.supplier.addressCity || '',
  supplier_address_postal_code: props.supplier.addressPostalCode || '',
  supplier_address_country: props.supplier.addressCountry || '',

  customer_business_name: props.customer.businessName || '',
  customer_business_id: props.customer.businessId || '',
  customer_vat_id: props.customer.vatId || '',
  customer_eu_vat_id: props.customer.euVatId || '',
  customer_email: props.customer.email || '',
  customer_phone_number: props.customer.phoneNumber || '',
  customer_website: props.customer.website || '',
  customer_additional_info: props.customer.additionalInfo || '',
  customer_address_line_one: props.customer.addressLineOne || '',
  customer_address_line_two: props.customer.addressLineTwo || '',
  customer_address_line_three: props.customer.addressLineThree || '',
  customer_address_city: props.customer.addressCity || '',
  customer_address_postal_code: props.customer.addressPostalCode || '',
  customer_address_country: props.customer.addressCountry || '',

  template: props.template,
  footer_note: props.footerNote || '',
  issued_by: props.issuedBy || '',
  issued_by_email: props.issuedByEmail || '',
  issued_by_phone_number: props.issuedByPhoneNumber || '',
  issued_by_website: props.issuedByWebsite || '',

  payment_method: props.paymentMethod,
  bank_name: props.bankName || '',
  bank_address: props.bankAddress || '',
  bank_bic: props.bankBic || '',
  bank_account_number: props.bankAccountNumber || '',
  bank_account_iban: props.bankAccountIban || '',
  variable_symbol: props.variableSymbol || '',
  specific_symbol: props.specificSymbol || '',
  constant_symbol: props.constantSymbol || '',
  show_pay_by_square: props.showPayBySquare,

  vat_reverse_charge: props.vatReverseCharge,
  vat_enabled: props.vatEnabled,

  lines: [] as Array<any>, // Array<InvoiceLine>
}))
const save = () =>{
  // TODO: Check či sa daju uložiť zmeny

  form.patch(route('invoices.update', props.id), {
    onSuccess: () => {
      toast.success('Zmeny boli uložené.')
    },
    onError: errors => {
      console.log(errors)
    }
  })
}

const addLine = () => {
  const newLines = form.lines.map(it => ({ ...it }))
  newLines.push(createInvoiceLine())
  form.lines = newLines
}

const { Meta_S, Ctrl_S } = useMagicKeys({
  passive: false,
  onEventFired(e) {
    if (e.key === 's' && (e.metaKey || e.ctrlKey)) {
      e.preventDefault()
    }
  },
})

watch([Meta_S, Ctrl_S], (v) => {
  if (v[0] || v[1]) {
    save()
  }
})
</script>
