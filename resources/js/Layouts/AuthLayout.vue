<template>
  <AuthLayout :title="title" :description="description">
    <slot/>
  </AuthLayout>
</template>

<script setup lang="ts">
import AuthLayout from '@/Layouts/Auth/AuthCardLayout.vue';

defineProps<{
  title?: string;
  description?: string;
}>();
</script>
