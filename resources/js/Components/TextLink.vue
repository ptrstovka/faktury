<template>
  <Link
    :href="href"
    :tabindex="tabindex"
    :method="method"
    :as="as"
    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
  >
    <slot/>
  </Link>
</template>

<script setup lang="ts">
import { type Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';

interface Props {
  href: string;
  tabindex?: number;
  method?: Method;
  as?: string;
}

defineProps<Props>();
</script>
