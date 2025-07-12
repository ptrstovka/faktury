<template>
  <DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
      <UserInfo :user="user" :show-email="true"/>
    </div>
  </DropdownMenuLabel>
  <DropdownMenuSeparator/>
  <DropdownMenuGroup>
    <DropdownMenuLabel>Firma</DropdownMenuLabel>
    <DropdownMenuCheckboxItem
      v-for="account in user.accounts"
      :model-value="account.current"
      class="pl-10"
      @select="account.current ? () => {} : switchAccount(account.id)"
    >{{ account.name }}</DropdownMenuCheckboxItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator/>
  <DropdownMenuGroup>
    <DropdownMenuItem :as-child="true">
      <Link class="block w-full" :href="route('profile.edit')" prefetch as="button">
        <Settings class="mr-2 h-4 w-4"/>
        Nastavenia
      </Link>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator/>
  <DropdownMenuItem :as-child="true">
    <Link class="block w-full" method="post" :href="route('logout')" @click="handleLogout" as="button">
      <LogOut class="mr-2 h-4 w-4"/>
      Odhlásiť sa
    </Link>
  </DropdownMenuItem>
</template>

<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuCheckboxItem,
  DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';

defineProps<{
  user: User
}>()

const handleLogout = () => router.flushAll()

const switchAccount = (id: number) => {
  router.post(route('accounts.switch', id))
}
</script>
