<template>
  <AuthBase title="Create an account" description="Enter your details below to create your account">
    <Head title="Register"/>

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid gap-6">
        <FormControl for="name" label="Name" :error="form.errors.name">
          <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name"/>
        </FormControl>

        <FormControl for="email" label="Email address" :error="form.errors.email">
          <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com"/>
        </FormControl>

        <FormControl for="password" label="Password" :error="form.errors.password">
          <Input
            id="password"
            type="password"
            required
            :tabindex="3"
            autocomplete="new-password"
            v-model="form.password"
            placeholder="Password"
          />
        </FormControl>

        <FormControl for="password_confirmation" label="Confirm password" :error="form.errors.password_confirmation">
          <Input
            id="password_confirmation"
            type="password"
            required
            :tabindex="4"
            autocomplete="new-password"
            v-model="form.password_confirmation"
            placeholder="Confirm password"
          />
        </FormControl>

        <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing" :processing="form.processing">
          Create account
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Already have an account?
        <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
      </div>
    </form>
  </AuthBase>
</template>

<script setup lang="ts">
import { FormControl } from '@/Components/Form'
import TextLink from '@/Components/TextLink.vue';
import { Button } from '@/Components/Button';
import { Input } from '@/Components/Input';
import AuthBase from '@/Layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
