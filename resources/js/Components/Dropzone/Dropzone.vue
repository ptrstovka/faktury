<template>
  <div ref="zoneEl" :class="cn(
    'relative flex flex-col w-full items-center justify-center text-sm border-2 overflow-hidden border-dashed rounded-md py-8 px-3 transition-colors',
    isOverDropZone ? 'border-primary' : 'border-input',
    $attrs.class || ''
  )">
    <UploadIcon class="size-6 mb-2 text-primary" />
    <span class="font-medium">Natiahnite súbor sem</span>
    <span class="text-muted-foreground mt-2">alebo</span>
    <Button @click.prevent.stop="selectFile" variant="link">vyberte súbor z počítača</Button>
    <input ref="inputEl" type="file" class="hidden" :multiple="multiple" :accept="accept" @change="onInputChange">

    <div v-if="processing" class="bg-background absolute inset-0 flex flex-col items-center justify-center">
      <span class="font-medium mb-4">Prebieha nahrávanie…</span>
      <Spinner class="size-4" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { cn } from '@/Utils'
import { Button } from '@/Components/Button'
import { UploadIcon } from 'lucide-vue-next'
import { useDropZone } from '@vueuse/core'
import { computed, ref } from "vue";
import { Spinner } from '@/Components/Spinner'

const emit = defineEmits<{
  (e: 'files', file: Array<File>): void
}>()
const props = withDefaults(defineProps<{
  multiple?: boolean
  allowed?: Array<string>
  processing?: boolean
}>(), {
  multiple: true,
})

const zoneEl = ref<HTMLDivElement>()
const inputEl = ref<HTMLInputElement>()

const onFiles = (files: Array<File> | null) => {
  if (files && files.length > 0) {
    emit('files', props.multiple === true ? files : [files[0]])
  }
}

const { isOverDropZone } = useDropZone(zoneEl, {
  onDrop: onFiles,
  multiple: props.multiple,
  dataTypes: props.allowed,
})

const selectFile = () => {
  inputEl.value?.click()
}

const onInputChange = () => {
  const files = inputEl.value?.files
  if (files) {
    onFiles(Array.from(files))
  }
}

const accept = computed(() => props.allowed?.join(','))
</script>
