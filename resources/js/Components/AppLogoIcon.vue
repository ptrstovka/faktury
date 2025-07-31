<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54.1 54.1" style="enable-background:new 0 0 54.1 54.1;" :class="className" v-bind="$attrs">
    <path fill="currentColor" d="M27,0C12.1,0,0,12.1,0,27c0,14.9,12.1,27,27,27c14.9,0,27-12.1,27-27C54,12.1,41.9,0,27,0z M43.3,19l-8.1,1.8l-8,1.8l8,1.8v7.3v0V39L27,40.8l-8.1,1.8v-7.3l8.1-1.8l8-1.8l-8-1.8l-8.1-1.8l-8.1-1.8v0v-7.3v0l8.1-1.8l8.1-1.8l8.1-1.8l8.1-1.8V19z"/>
  </svg>
</template>

<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>
