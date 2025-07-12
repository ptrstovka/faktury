<template>
  <BreadcrumbList>
    <template v-for="(item, idx) in list">
      <BreadcrumbItem>
        <BreadcrumbLink
          v-if="item.action && item.action.type === 'link'"
          :href="item.action.link.url"
          :as="item.action.link.external ? 'a' : undefined"
        >
          {{ item.title }}
        </BreadcrumbLink>
        <BreadcrumbPage v-else-if="idx === list.length - 1">{{ item.title }}</BreadcrumbPage>
        <template v-else>{{ item.title }}</template>
      </BreadcrumbItem>
      <BreadcrumbSeparator v-if="idx < list.length - 1" />
    </template>
  </BreadcrumbList>
</template>

<script setup lang="ts">
import { type BreadcrumbNavigationList } from '@stacktrace/ui'
import BreadcrumbList from './BreadcrumbList.vue'
import BreadcrumbItem from './BreadcrumbItem.vue'
import BreadcrumbLink from './BreadcrumbLink.vue'
import BreadcrumbPage from './BreadcrumbPage.vue'
import BreadcrumbSeparator from './BreadcrumbSeparator.vue'

defineProps<{
  list: BreadcrumbNavigationList
}>()
</script>
