<template>
  <Dialog :control="control">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Odoslať faktúru</DialogTitle>
        <DialogDescription>Zadajte emailovú adresu na ktorú bude táto faktúra odoslaná</DialogDescription>
      </DialogHeader>
      <div class="flex flex-col gap-4">
        <FormControl label="E-Mail" :error="form.errors.email">
          <Input v-model="form.email" />
        </FormControl>

        <FormControl label="Správa" help="Toto pole podporuje Markdown syntax." :error="form.errors.message">
          <Textarea v-model="form.message" rows="6" />
        </FormControl>
      </div>
      <DialogFooter>
        <Button @click="control.deactivate" variant="outline">Zrušiť</Button>
        <Button :processing="form.processing" @click="send">Odoslať</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogTitle, DialogHeader, DialogFooter, DialogContent, DialogDescription } from '@/Components/Dialog'
import { Button } from '@/Components/Button'
import { FormControl } from "@/Components/Form";
import { Input } from "@/Components/Input";
import { Textarea } from "@/Components/Textarea";
import { useForm } from "@inertiajs/vue3";
import { onActivated, type Toggle } from "@stacktrace/ui";

const props = defineProps<{
  control: Toggle
  id: string
  email?: string
  message?: string
}>()

const form = useForm(() => ({
  email: props.email || '',
  message: props.message || '',
}))

onActivated(props.control, () => {
  form.clearErrors()
  form.reset()
})

const send = () => {
  form.post(route('invoices.send', props.id), {
    onSuccess: () => {
      props.control.deactivate()
    },
    preserveScroll: true,
  })
}
</script>
