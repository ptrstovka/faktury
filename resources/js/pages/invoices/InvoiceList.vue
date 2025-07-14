<template>
  <Head title="Faktúry"/>

  <AppLayout>
    <div class="flex flex-col items-start">
      <p>zoznam faktur</p>
      <Button :processing="draft.processing" @click="createDraft">Nová faktúra</Button>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from "@inertiajs/vue3";
import { Button } from '@/components/ui/button'

const draft = useForm(() => ({}))
const createDraft = () => draft.post(route('invoices.store'))
</script>
