<template>
  <Head title="Faktúra" />

  <AppLayout class="pb-16">
    <InvoiceFormRoot :context="context">
      <div class="px-4">
        <div class="flex flex-row justify-between items-center border-b py-4 mb-4">
          <div class="">
            <p class="text-2xl font-medium text-muted-foreground" v-if="draft">Nová faktúra</p>
            <div class="inline-flex items-center gap-2" v-else>
              <p class="text-2xl font-medium">Faktúra {{ publicInvoiceNumber }}</p>
              <LockIcon v-if="locked" class="size-4 text-yellow-400" />
              <LockOpenIcon v-else class="size-4 text-destructive" />
            </div>
          </div>

          <div class="flex gap-2">
            <template v-if="draft">
              <Button @click="save" variant="ghost" size="sm">Zahodiť koncept</Button>
              <Button :processing="isSaving" @click="save" variant="outline" size="sm" label="Uložiť koncept" :icon="SaveIcon" />
              <Button :processing="isIssuing" @click="issueInvoice" size="sm" label="Vystaviť" :icon="ClipboardCheckIcon" />
            </template>

            <template v-else>
              <template v-if="locked">
                <Button size="sm" label="Stiahnuť" :icon="FileDownIcon" />
                <Button variant="outline" size="sm" label="Odoslať" :icon="SendIcon" />
                <Button variant="outline" size="sm" label="Pridať úhradu" :icon="BanknoteIcon" />
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button class="px-2" size="sm" variant="outline" :icon="EllipsisIcon" />
                  </DropdownMenuTrigger>
                  <DropdownMenuContent class="min-w-48" align="end">
                    <DropdownMenuItem @select="unlockInvoice">
                      <FileLockIcon /> Odomknúť úpravy
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem variant="destructive">
                      <Trash2Icon /> Odstrániť
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </template>
              <template v-else>
                <Button :disabled="!form.isDirty" :processing="isSaving" @click="save" variant="outline" size="sm" label="Uložiť zmeny" :icon="SaveIcon" />

                <Button :processing="lockInvoiceForm.processing" @click="lockInvoice" size="sm" label="Zamknúť úpravy" :icon="KeySquareIcon" />
              </template>
            </template>
          </div>
        </div>

        <div class="space-y-12">
          <div class="grid grid-cols-2 gap-8">
            <InvoiceFormSectionNumbering />

            <InvoiceFormSectionDates />
          </div>

          <div class="grid grid-cols-2 gap-8">
            <InvoiceFormSectionSupplier />

            <InvoiceFormSectionCustomer />
          </div>

          <InvoiceFormSectionLines />

          <div class="grid grid-cols-2 gap-8">
            <InvoiceFormSectionSettings />

            <InvoiceFormSectionPayment />
          </div>
        </div>
      </div>
    </InvoiceFormRoot>
  </AppLayout>
</template>

<script setup lang="ts">
import InvoiceFormSectionCustomer from "./Form/InvoiceFormSectionCustomer.vue";
import InvoiceFormSectionDates from "./Form/InvoiceFormSectionDates.vue";
import InvoiceFormSectionLines from "./Form/InvoiceFormSectionLines.vue";
import InvoiceFormSectionNumbering from "./Form/InvoiceFormSectionNumbering.vue";
import InvoiceFormSectionPayment from "./Form/InvoiceFormSectionPayment.vue";
import InvoiceFormSectionSettings from "./Form/InvoiceFormSectionSettings.vue";
import InvoiceFormSectionSupplier from "./Form/InvoiceFormSectionSupplier.vue";
import InvoiceFormRoot from "./Form/InvoiceFormRoot.vue";
import type { InvoiceDetailProps } from ".";
import { useInvoiceForm } from './Form'
import { Button } from "@/Components/Button"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from "@/Components/DropdownMenu"
import AppLayout from "@/Layouts/AppLayout.vue"
import { notifyAboutFirstVisibleError, useSaveShortcut } from "@/Utils";
import { Head, router, useForm } from "@inertiajs/vue3"
import { SaveIcon, SendIcon, FileDownIcon, FileLockIcon, EllipsisIcon, LockIcon, LockOpenIcon, KeySquareIcon, Trash2Icon, BanknoteIcon, ClipboardCheckIcon } from "lucide-vue-next"
import { computed, ref } from "vue"
import { toast } from "vue-sonner"

const props = defineProps<InvoiceDetailProps>()

const context = useInvoiceForm(computed(() => props))
const { form } = context

const isSaving = ref(false)
const isIssuing = ref(false)

const save = () => {
  if (props.locked) {
    return
  }

  isSaving.value = true

  form.patch(route("invoices.update", props.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success("Zmeny boli uložené.")
    },
    onFinish: () => {
      isSaving.value = false
    },
    onError: () => {
      notifyAboutFirstVisibleError('Niektoré polia sa nepodarilo uložiť.')
    },
  })
}

const issueInvoice = () => {
  isIssuing.value = true

  form.post(route("invoices.issue", props.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success("Faktúra bola vystavená")
      form.reset()
    },
    onFinish: () => {
      isIssuing.value = false
    },
    onError: () => {
      notifyAboutFirstVisibleError('Faktúru sa nepodarilo vystaviť.')
    },
  })
}

const lockInvoiceForm = useForm({})
const lockInvoice = () => {
  lockInvoiceForm.post(route("invoices.lock.store", props.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success("Faktúra bola uzamknutá")
      },
    },
  )
}

const unlockInvoice = () => {
  router.delete(route("invoices.lock.destroy", props.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success("Faktúra bola odomknutá")
    },
  })
}

useSaveShortcut(() => save())
</script>
