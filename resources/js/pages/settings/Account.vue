<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Firma"/>

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Základné údaje" description="Nastavte si základné údaje o Vašej firme"/>

        <p>serus</p>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Firma',
    href: '/settings/appearance',
  },
];
</script>
