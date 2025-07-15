<template>
  <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
    <Head title="Log in"/>

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <FormControl for="email" label="Email address" :error="form.errors.email || errors?.email">
          <Input
            id="email"
            type="email"
            required
            autofocus
            :tabindex="1"
            autocomplete="email"
            v-model="form.email"
            placeholder="email@example.com"
          />
        </FormControl>

        <FormControl for="password" label="Password" :error="form.errors.password">
          <Input
            id="password"
            type="password"
            required
            :tabindex="2"
            autocomplete="current-password"
            v-model="form.password"
            placeholder="Password"
          />

          <TextLink v-if="canResetPassword" :href="route('password.request')" class="block text-sm mt-4" :tabindex="5">
            Forgot password?
          </TextLink>
        </FormControl>

        <div class="flex items-center justify-between">
          <Label for="remember" class="flex items-center space-x-3">
            <Checkbox id="remember" v-model="form.remember" :tabindex="3"/>
            <span>Remember me</span>
          </Label>
        </div>

        <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing" :processing="form.processing">
          Log in
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Don't have an account?
        <TextLink :href="route('register')" :tabindex="6">Sign up</TextLink>
      </div>
    </form>
  </AuthBase>
</template>

<script setup lang="ts">
import { FormControl } from '@/Components/Form'
import TextLink from '@/Components/TextLink.vue';
import { Button } from '@/Components/Button';
import { Checkbox } from '@/Components/Checkbox';
import { Input } from '@/Components/Input';
import { Label } from '@/Components/Label';
import AuthBase from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
  status?: string;
  canResetPassword: boolean;
  errors?: Record<string, string>
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>
