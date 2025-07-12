<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Profile settings"/>

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall title="Profile information" description="Update your name and email address"/>

        <form @submit.prevent="submit" class="space-y-6">
          <FormControl for="name" label="Name" :error="form.errors.name">
            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name"/>
          </FormControl>

          <FormControl for="email" label="Email address" :error="form.errors.email">
            <Input
              id="email"
              type="email"
              class="mt-1 block w-full"
              v-model="form.email"
              required
              autocomplete="username"
              placeholder="Email address"
            />
          </FormControl>

          <div v-if="mustVerifyEmail && !user.email_verified_at">
            <p class="-mt-4 text-sm text-muted-foreground">
              Your email address is unverified.
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
              >
                Click here to resend the verification email.
              </Link>
            </p>

            <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
              A new verification link has been sent to your email address.
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="form.processing" :processing="form.processing" :recently-successful="form.recentlySuccessful">Save
            </Button>
          </div>
        </form>
      </div>

      <DeleteUser/>
    </SettingsLayout>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import { FormControl } from '@/components/ui/form'
import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: '/settings/profile',
  },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
  name: user.name,
  email: user.email,
});

const submit = () => {
  form.patch(route('profile.update'), {
    preserveScroll: true,
  });
};
</script>
