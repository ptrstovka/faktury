<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property \App\Models\Company $company
 * @property boolean $vat_enabled
 */
class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $guarded = false;

    protected function casts(): array
    {
        return [
            'invoice_payment_method' => PaymentMethod::class,
            'vat_enabled' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['role']);
    }
}
