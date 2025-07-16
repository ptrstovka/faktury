<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \App\Models\Upload|null $signature
 * @property \App\Models\Upload|null $logo
 * @property \App\Models\Company $supplier
 * @property \App\Models\Company $customer
 * @property \Carbon\Carbon|null $issued_at
 * @property \Carbon\Carbon|null $supplied_at
 * @property \Carbon\Carbon|null $payment_due_to
 * @property PaymentMethod $payment_method
 * @property string|null $variable_symbol
 * @property string|null $specific_symbol
 * @property string|null $constant_symbol
 * @property boolean $show_pay_by_square
 * @property boolean $vat_reverse_charge
 */
class Invoice extends Model
{
    use HasUuid;

    protected $guarded = false;

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'supplied_at' => 'date',
            'payment_due_to' => 'date',
            'payment_method' => PaymentMethod::class,
            'show_pay_by_square' => 'boolean',
            'vat_reverse_charge' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
