<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

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
 * @property string|null $public_invoice_number
 * @property string $currency
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceLine> $lines
 * @property \App\Models\Account $account
 * @property boolean $draft
 * @property boolean $sent
 * @property boolean $paid
 * @property boolean $locked
 * @property boolean $vat_enabled
 * @property string $locale
 * @property string $template
 * @property string|null $footer_note
 * @property string|null $issued_by
 * @property string|null $issued_by_email
 * @property string|null $issued_by_phone_number
 * @property string|null $issued_by_website
 */
class Invoice extends Model
{
    use HasUuid;

    protected $guarded = false;

    protected function casts(): array
    {
        return [
            'draft' => 'boolean',
            'sent' => 'boolean',
            'paid' => 'boolean',
            'locked' => 'boolean',
            'issued_at' => 'date',
            'supplied_at' => 'date',
            'payment_due_to' => 'date',
            'payment_method' => PaymentMethod::class,
            'show_pay_by_square' => 'boolean',
            'vat_reverse_charge' => 'boolean',
            'vat_enabled' => 'boolean',
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

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Get lines sorted by a position attribute.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceLine>
     */
    public function getSortedLines(): Collection
    {
        return $this->lines->sortBy('position')->values();
    }

    /**
     * Issue an invoice.
     */
    public function issue(): void
    {
        if (! $this->draft) {
            throw new RuntimeException("The invoice is already issued");
        }

        // TODO: Generovat cislo faktury
        // TODO: Generovat variabilny symbol

        $this->draft = false;
        $this->locked = true;

        $this->save();
    }

    /**
     * Add edit lock on the invoice.
     */
    public function lock(): void
    {
        if ($this->draft) {
            throw new RuntimeException("The invoice draft cannot be locked");
        }

        $this->locked = true;
        $this->save();
    }

    /**
     * Remove edit lock from the invoice.
     */
    public function unlock(): void
    {
        $this->locked = false;
        $this->save();
    }
}
