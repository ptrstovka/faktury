<template>
  <AlertDialog :control="control">
    <AlertDialogContent v-if="dialog" :to="to">
      <AlertDialogHeader>
        <AlertDialogTitle>{{ dialog.title || 'Potvrdenie' }}</AlertDialogTitle>
        <AlertDialogDescription>{{ dialog.message || 'Naozaj checete spustiť túto akciu?' }}</AlertDialogDescription>
      </AlertDialogHeader>

      <AlertDialogFooter>
        <Button @click="cancel" :processing="isCancelling" variant="outline">{{ dialog.cancelLabel || 'Zrušiť' }}</Button>
        <Button @click="confirm" :processing="isConfirming" :variant="dialog.destructive ? 'destructive' : 'default'">{{ dialog.confirmLabel || 'Potvrdiť' }}</Button>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

<script setup lang="ts">
import { useConfirmationDialogRoot } from "."
import { ref } from "vue"
import { onDeactivated } from "@stacktrace/ui"
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogTitle, AlertDialogDescription, AlertDialogFooter } from '@/Components/AlertDialog'
import { Button } from '@/Components/Button'

defineProps<{
  to?: string | HTMLElement
}>()

const { control, dialog, close: closeDialog } = useConfirmationDialogRoot()

const isCancelling = ref(false)
const isConfirming = ref(false)

const ANIMATION_DELAY = 300

const close = () => closeDialog(ANIMATION_DELAY)

const cancel = async () => {
  const cancelCallback = dialog.value?.cancel

  if (! cancelCallback) {
    close()
    return
  }

  isCancelling.value = true

  await cancelCallback()
  close()
}

const confirm = async () => {
  const confirmCallback = dialog.value?.confirm

  if (! confirmCallback) {
    close()
    return
  }

  isConfirming.value = true

  await confirmCallback()
  close()
}

onDeactivated(control, () => {
  setTimeout(() => {
    isCancelling.value = false
    isConfirming.value = false
  }, ANIMATION_DELAY)
})
</script>
