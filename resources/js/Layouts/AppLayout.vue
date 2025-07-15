<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <slot/>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/Types';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});
</script>
