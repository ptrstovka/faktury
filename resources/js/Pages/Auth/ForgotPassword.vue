<template>
  <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
    <Head title="Forgot password"/>

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <div class="space-y-6">
      <form @submit.prevent="submit">
        <FormControl for="email" label="Email address" :error="form.errors.email">
          <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com"/>
        </FormControl>

        <div class="my-6 flex items-center justify-start">
          <Button class="w-full" :disabled="form.processing" :processing="form.processing">
            Email password reset link
          </Button>
        </div>
      </form>

      <div class="space-x-1 text-center text-sm text-muted-foreground">
        <span>Or, return to</span>
        <TextLink :href="route('login')">log in</TextLink>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { FormControl } from '@/Components/Form'
import TextLink from '@/Components/TextLink.vue';
import { Button } from '@/Components/Button';
import { Input } from '@/Components/Input';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
  status?: string;
}>();

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>
