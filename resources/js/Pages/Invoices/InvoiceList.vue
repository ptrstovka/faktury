<template>
  <Head title="Faktúry"/>

  <AppLayout class="pb-12">
    <div class="flex flex-row items-end justify-between pt-6 px-4">
      <Heading title="Vystavené faktúry" class="mb-0" />

      <div class="inline-flex flex-row gap-2">
        <Button v-if="! invoices.isEmpty" :processing="draft.processing" @click="createDraft" size="sm" label="Nová faktúra" :icon="PlusIcon" />
      </div>
    </div>

    <div class="px-4">
      <DataTable
        :table="invoices"
        inset-left="pl-1"
        inset-right="pr-1"
        empty-table-message="Žiadne vystavené faktúry"
        empty-table-description="Zatiaľ neboli vystavené žiadne faktúry."
        empty-results-message="Žiadne výsledky"
        empty-results-description="Neboli nájdené žiadne vystavené faktúry."
      >
        <template #empty-table>
          <Button class="mt-4" :processing="draft.processing" @click="createDraft" size="sm" label="Nová faktúra" :icon="PlusIcon" />
        </template>
      </DataTable>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { type DataTableValue, DataTable } from "@/Components/DataTable";
import Heading from "@/Components/Heading.vue";
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from "@inertiajs/vue3";
import { Button } from '@/Components/Button'
import { PlusIcon } from 'lucide-vue-next'

defineProps<{
  invoices: DataTableValue
}>()

const draft = useForm(() => ({}))
const createDraft = () => draft.post(route('invoices.store'))
</script>
