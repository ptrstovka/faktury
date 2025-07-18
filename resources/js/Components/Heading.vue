<template>
  <div :class="cn('mb-8 space-y-0.5', $attrs.class || '')">
    <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
    <p v-if="description" class="text-sm text-muted-foreground">
      {{ description }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { cn } from "@/Utils";

interface Props {
  title: string;
  description?: string;
}

defineProps<Props>();
</script>
