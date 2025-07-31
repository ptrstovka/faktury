<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Vzhľad"/>

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Nastavenie vzhľadu" description="Nastavte si preferovaný vzhľad aplikácie"/>
        <AppearanceTabs/>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/Components/AppearanceTabs.vue';
import HeadingSmall from '@/Components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/Types';

import AppLayout from '@/Layouts/AppLayout.vue';
import SettingsLayout from '@/Layouts/Settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Appearance settings',
    href: '/settings/appearance',
  },
];
</script>
