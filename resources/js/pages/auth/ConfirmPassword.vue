<template>
  <AuthLayout title="Confirm your password" description="This is a secure area of the application. Please confirm your password before continuing.">
    <Head title="Confirm password"/>

    <form @submit.prevent="submit">
      <div class="space-y-6">
        <FormControl for="password" label="Password" :error="form.errors.password">
          <Input
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="current-password"
            autofocus
          />
        </FormControl>

        <div class="flex items-center">
          <Button class="w-full" :disabled="form.processing" :processing="form.processing">
            Confirm Password
          </Button>
        </div>
      </div>
    </form>
  </AuthLayout>
</template>

<script setup lang="ts">
import { FormControl } from '@/components/ui/form'
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  password: '',
});

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => {
      form.reset();
    },
  });
};
</script>
