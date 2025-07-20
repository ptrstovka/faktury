<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function view(User $user, Account $account): bool
    {
        return $user->accounts->contains($account);
    }

    public function update(User $user, Account $account): bool
    {
        return $user->accounts->contains($account);
    }
}
