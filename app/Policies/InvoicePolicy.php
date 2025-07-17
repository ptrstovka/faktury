<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->accounts->contains($invoice->account);
    }

    public function create(User $user): bool
    {
        return $user->accounts->isNotEmpty();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->accounts->contains($invoice->account);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->accounts->contains($invoice->account);
    }
}
