<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property \App\Models\Company $company
 * @property boolean $vat_enabled
 * @property string $invoice_numbering_format
 * @property string $invoice_variable_symbol_format
 * @property float $default_vat_rate
 * @property int $invoice_due_days
 * @property \App\Enums\PaymentMethod $invoice_payment_method
 * @property string|null $invoice_footer_note
 * @property string $invoice_template
 * @property \App\Models\Upload|null $invoiceSignature
 * @property \App\Models\Upload|null $invoiceLogo
 * @property int $next_invoice_number
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\NumberSequence> $numberSequences
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

    public function invoiceSignature(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function invoiceLogo(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }

    public function numberSequences(): HasMany
    {
        return $this->hasMany(NumberSequence::class);
    }
}
