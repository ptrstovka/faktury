<template>
  <component
    :is="as"
    :as-child="action ? action.type === 'link' : false"
    :tooltip="title || undefined"
    v-bind="$attrs"
  >
    <template v-if="action && action.type === 'link'">
      <component :is="action.link.external ? 'a' : Link" :href="action.link.url">
        <Icon v-if="icon" :src="icon.src" class="size-4" />
        <span>{{ title }}</span>
        <SidebarMenuBadge v-if="badge">{{ badge }}</SidebarMenuBadge>
        <slot />
      </component>
    </template>
    <template v-else>
      <Icon v-if="icon" :src="icon.src" class="size-4" />
      <span>{{ title }}</span>
      <SidebarMenuBadge v-if="badge">{{ badge }}</SidebarMenuBadge>
      <slot />
    </template>
  </component>
</template>

<script setup lang="ts">
import SidebarMenuBadge from './SidebarMenuBadge.vue'
import { Link } from '@inertiajs/vue3'
import { computed, type Component } from 'vue'
import { type NavigationItem, Icon } from '@stacktrace/ui'

const props = defineProps<{
  item: NavigationItem
  as: Component
}>()

const action = computed(() => props.item.item.action)
const title = computed(() => props.item.item.title)
const icon = computed(() => props.item.item.icon)
const badge = computed(() => props.item.item.badge)
</script>
