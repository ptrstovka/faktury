<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Password settings"/>

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall title="Update password" description="Ensure your account is using a long, random password to stay secure"/>

        <form @submit.prevent="updatePassword" class="space-y-6">
          <FormControl for="current_password" label="Current password" :error="form.errors.current_password">
            <Input
              id="current_password"
              ref="currentPasswordInput"
              v-model="form.current_password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="current-password"
              placeholder="Current password"
            />
          </FormControl>

          <FormControl for="password" label="New password" :error="form.errors.password">
            <Input
              id="password"
              ref="passwordInput"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="new-password"
              placeholder="New password"
            />
          </FormControl>

          <FormControl for="password_confirmation" label="Confirm password" :error="form.errors.password_confirmation">
            <Input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              class="mt-1 block w-full"
              autocomplete="new-password"
              placeholder="Confirm password"
            />
          </FormControl>


          <div class="flex items-center gap-4">
            <Button :disabled="form.processing" :processing="form.processing" :recently-successful="form.recentlySuccessful">Save password
            </Button>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { FormControl } from '@/components/ui/form'
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Password settings',
    href: '/settings/password',
  },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: (errors: any) => {
      if (errors.password) {
        form.reset('password', 'password_confirmation');
        if (passwordInput.value instanceof HTMLInputElement) {
          passwordInput.value.focus();
        }
      }

      if (errors.current_password) {
        form.reset('current_password');
        if (currentPasswordInput.value instanceof HTMLInputElement) {
          currentPasswordInput.value.focus();
        }
      }
    },
  });
};
</script>
