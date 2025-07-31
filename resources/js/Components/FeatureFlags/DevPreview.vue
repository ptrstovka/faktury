<template>
<span class="beta-badge inline-flex uppercase font-bold text-[10px] leading-[13px] text-white px-[4px] pb-[1px] tracking-wide rounded">
  <slot>DEV Preview</slot>
</span>
</template>
<style scoped>
.beta-badge {
  background: rgb(131,58,180);
  background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(253,29,29,1) 77%);
}
</style>
