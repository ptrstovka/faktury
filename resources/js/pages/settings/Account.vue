<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Firma"/>

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Základné informácie" description="Nastavte si základné údaje o Vašej firme"/>

        <form @submit.prevent="saveGeneral" class="space-y-6">
          <FormControl label="Obchodné meno" :error="general.errors.business_name">
            <Input v-model="general.business_name" />
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="IČO" :error="general.errors.business_id">
              <Input v-model="general.business_id" />
            </FormControl>

            <FormControl label="DIČ" :error="general.errors.vat_id">
              <Input v-model="general.vat_id" />
            </FormControl>

            <FormControl label="IČDPH" :error="general.errors.eu_vat_id">
              <Input v-model="general.eu_vat_id" />
            </FormControl>
          </div>

          <FormControl label="Adresa" :error="general.errors.address_line_one || general.errors.address_line_two || general.errors.address_line_three">
            <div class="flex flex-col gap-2">
              <Input v-model="general.address_line_one" />
              <Input v-model="general.address_line_two" />
              <!--<Input v-model="general.address_line_three" />-->
            </div>
          </FormControl>

          <div class="grid grid-cols-3 gap-4">
            <FormControl label="PSČ" :error="general.errors.address_postal_code">
              <Input v-model="general.address_postal_code" />
            </FormControl>

            <FormControl label="Mesto" :error="general.errors.address_city" class="col-span-2">
              <Input v-model="general.address_city" />
            </FormControl>
          </div>

          <FormControl label="Krajina" :error="general.errors.address_country_code">
            <FormSelect v-model="general.address_country_code" :options="countries" />
          </FormControl>

          <FormControl label="Webová stránka" :error="general.errors.website" class="max-w-xs">
            <Input v-model="general.website" />
          </FormControl>

          <FormControl label="E-Mail" :error="general.errors.email" class="max-w-xs">
            <Input v-model="general.email" />
          </FormControl>

          <FormControl label="Telefón" :error="general.errors.phone_number" class="max-w-xs">
            <Input v-model="general.phone_number" />
          </FormControl>

          <Button :processing="general.processing" :recently-successful="general.recentlySuccessful">Uložiť</Button>
        </form>
      </div>
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
import { type SelectOption } from '@stacktrace/ui'
import { Button } from '@/components/ui/button'

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Firma',
    href: '/settings/appearance',
  },
]

const props = defineProps<{
  id: number
  countries: Array<SelectOption>
  businessName: string | null
  businessId: string | null
  vatId: string | null
  euVatId: string | null
  addressLineOne: string | null
  addressLineTwo: string | null
  addressLineThree: string | null
  addressCity: string | null
  addressPostalCode: string | null
  addressCountryCode: string | null
  website: string | null
  email: string | null
  phoneNumber: string | null
}>()

const general = useForm(() => ({
  business_name: props.businessName || '',
  business_id: props.businessId || '',
  vat_id: props.vatId || '',
  eu_vat_id: props.euVatId || '',
  address_line_one: props.addressLineOne || '',
  address_line_two: props.addressLineTwo || '',
  address_line_three: props.addressLineThree || '',
  address_city: props.addressCity || '',
  address_postal_code: props.addressPostalCode || '',
  address_country_code: props.addressCountryCode || '',
  website: props.website || '',
  email: props.email || '',
  phone_number: props.phoneNumber || '',
}))
const saveGeneral = () => {
  general.patch(route('accounts.update'), { preserveScroll: true })
}
</script>
