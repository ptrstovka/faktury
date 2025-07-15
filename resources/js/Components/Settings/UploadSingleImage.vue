<template>
  <FormControl :error="form.errors.file">
    <div
      v-if="src"
      :class="cn(
        'relative w-fit border border-dashed p-2 rounded-md',
        $attrs.class || ''
      )"
    >
      <img class="h-32" v-if="src" :src="src" alt="">
      <TooltipProvider :delay-duration="0">
        <Tooltip>
          <TooltipTrigger as-child>
            <button class="text-destructive absolute -top-4 -right-4 opacity-70 hover:opacity-100 p-2" @click="remove">
              <XCircleIcon class="size-4" />
            </button>
          </TooltipTrigger>
          <TooltipContent>Odstrániť</TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </div>

    <Dropzone
      v-if="!src || (form.processing && form.file)"
      @files="onFileSelected"
      :multiple="false"
      :processing="form.processing"
      :allowed="['image/jpg', 'image/jpeg', 'image/png']"
      :class="{ 'border-destructive': form.errors.file }"
    />
  </FormControl>
</template>

<script setup lang="ts">
import { Dropzone } from "@/Components/Dropzone"
import { FormControl } from "@/Components/Form"
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/Tooltip"
import { cn } from "@/Utils";
import { useForm } from "@inertiajs/vue3"
import { XCircleIcon } from "lucide-vue-next"

const props = defineProps<{
  url: string
  src: string | null
}>()

const form = useForm(() => ({
  _method: 'patch',
  file: null as File | null,
  remove_file: false as boolean,
}))
const save = () => {
  form.post(props.url, {
    preserveScroll: true,
    onSuccess: () => form.reset()
  })
}
const onFileSelected = (files: Array<File>) => {
  const file = files[0]

  form.file = file
  form.remove_file = false

  save()
}
const remove = () => {
  form.file = null
  form.remove_file = true

  save()
}
</script>
