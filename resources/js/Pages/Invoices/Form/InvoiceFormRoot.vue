<template>
  <slot />
</template>

<script setup lang="ts">
import { type InvoiceFormContext, provideInvoiceFormContext } from "."

const props = defineProps<{
  context: InvoiceFormContext
}>()

provideInvoiceFormContext(props.context)
</script>
