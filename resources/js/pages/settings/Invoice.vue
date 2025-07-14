<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Faktúry"/>

    <SettingsLayout>
      <section class="space-y-6">
        <HeadingSmall title="Nastavenie fakturácie" description="Prispôsobte si predvolené možnosti použité pri tvorbe nových faktúr"/>

        <form @submit.prevent="save" class="space-y-6">
          <FormControl label="Číselný formát" :error="form.errors.numbering_format">
            <Input v-model="form.numbering_format" />
          </FormControl>

          <FormControl label="Počet dní splatnosti" :error="form.errors.due_days">
            <Input v-model="form.due_days" class="max-w-36" />
          </FormControl>

          <FormControl v-if="vatEnabled" label="Sadzba DPH" :error="form.errors.default_vat_rate">
            <Input v-model="form.default_vat_rate" class="max-w-36" />
          </FormControl>

          <FormControl label="Spôsob platby" :error="form.errors.payment_method" class="max-w-sm">
            <FormSelect :options="paymentMethods" v-model="form.payment_method" />
          </FormControl>

          <FormControl label="Šablóna" :error="form.errors.template" class="max-w-sm">
            <FormSelect :options="templates" v-model="form.template" />
          </FormControl>

          <FormControl label="Informácia v pätičke" :error="form.errors.footer_note">
            <Textarea v-model="form.footer_note" rows="3" />
          </FormControl>

          <Button :processing="form.processing" :recently-successful="form.recentlySuccessful">Uložiť</Button>
        </form>
      </section>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { type BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import { FormControl, FormSelect } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { type SelectOption } from '@stacktrace/ui'
import { Button } from '@/components/ui/button'

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Invoice',
    href: '/settings/appearance',
  },
]

const props = defineProps<{
  vatEnabled: boolean
  numberingFormat: string
  defaultVatRate: number
  dueDays: number
  footerNote: string | null
  template: string
  templates: Array<SelectOption>
  paymentMethod: string
  paymentMethods: Array<SelectOption>
}>()

const form = useForm(() => ({
  numbering_format: props.numberingFormat,
  default_vat_rate: props.defaultVatRate,
  due_days: props.dueDays,
  footer_note: props.footerNote || '',
  template: props.template,
  payment_method: props.paymentMethod,
}))
const save = () => {
  form.patch(route('invoices.settings.update'), { preserveScroll: true })
}
</script>
