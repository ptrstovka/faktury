<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Faktúry"/>

    <SettingsLayout>
      <section class="space-y-6">
        <HeadingSmall title="Predvolené nastavenia faktúr" description="Prispôsobte si predvolené možnosti použité pri tvorbe nových faktúr"/>

        <form @submit.prevent="save" class="space-y-6">
          <FormControl label="Číselný formát" :error="form.errors.numbering_format">
            <Input v-model="form.numbering_format" class="max-w-sm" />
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

      <section class="space-y-6">
        <HeadingSmall title="Logo" description="Nastavte si logo zbrazené v hlavičke faktúry"/>

        <div class="space-y-6">
          <UploadSingleImage :url="route('invoices.settings.logo')" :src="logoFileUrl" />
        </div>
      </section>

      <section class="space-y-6">
        <HeadingSmall title="Podpis" description="Nastavte si podpis zbrazený na Vašich faktúrach"/>

        <div class="space-y-6">
          <UploadSingleImage :url="route('invoices.settings.signature')" :src="signatureFileUrl" />
        </div>
      </section>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { type BreadcrumbItem } from '@/Types'
import AppLayout from '@/Layouts/AppLayout.vue'
import SettingsLayout from '@/Layouts/settings/Layout.vue'
import { FormControl, FormSelect } from '@/Components/Form'
import { Input } from '@/Components/Input'
import { Textarea } from '@/Components/Textarea'
import { type SelectOption } from '@stacktrace/ui'
import { Button } from '@/Components/Button'
import UploadSingleImage from "@/components/settings/UploadSingleImage.vue";

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
  signatureFileUrl: string | null
  logoFileUrl: string | null
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
