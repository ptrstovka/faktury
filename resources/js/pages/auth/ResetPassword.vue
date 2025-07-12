<template>
  <AuthLayout title="Reset password" description="Please enter your new password below">
    <Head title="Reset password"/>

    <form @submit.prevent="submit">
      <div class="grid gap-6">
        <FormControl for="email" label="Email" :error="form.errors.email">
          <Input id="email" type="email" name="email" autocomplete="email" v-model="form.email" class="mt-1 block w-full" readonly/>
        </FormControl>

        <FormControl for="password" label="Password" :error="form.errors.password">
          <Input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            v-model="form.password"
            class="mt-1 block w-full"
            autofocus
            placeholder="Password"
          />
        </FormControl>

        <FormControl for="password_confirmation" label="Confirm Password" :error="form.errors.password_confirmation">
          <Input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            v-model="form.password_confirmation"
            class="mt-1 block w-full"
            placeholder="Confirm password"
          />
        </FormControl>

        <Button type="submit" class="mt-4 w-full" :disabled="form.processing" :processing="form.processing">
          Reset password
        </Button>
      </div>
    </form>
  </AuthLayout>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { FormControl } from '@/components/ui/form'
import { Input } from '@/components/ui/input';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
  token: string;
  email: string;
}

const props = defineProps<Props>();

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>
