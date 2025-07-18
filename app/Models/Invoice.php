<?php

namespace App\Models;

use App\Casts\AsMoney;
use App\Enums\PaymentMethod;
use App\Models\Concerns\HasUuid;
use App\NumberSequenceFormatter;
use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
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
 * @property \App\Models\NumberSequence|null $numberSequence
 * @property int $invoice_number
 * @property Money|null $total_vat_inclusive
 * @property Money|null $total_vat_exclusive
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
            'total_vat_inclusive' => AsMoney::class,
            'total_vat_exclusive' => AsMoney::class,
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

    public function numberSequence(): BelongsTo
    {
        return $this->belongsTo(NumberSequence::class);
    }

    /**
     * Get lines sorted by a position attribute.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceLine>
     */
    public function getSortedLines(): EloquentCollection
    {
        return $this->lines->sortBy('position')->values();
    }

    /**
     * Calculate invoice totals.
     */
    public function calculateTotals(): void
    {
        $sum = fn (Collection $prices) => Money::total(Money::zero($this->currency), ...$prices->filter()->values());

        $this->total_vat_inclusive = $sum($this->lines->map->total_price_vat_inclusive);
        $this->total_vat_exclusive = $sum($this->lines->map->total_price_vat_exclusive);

        $this->save();
    }

    /**
     * Issue an invoice.
     */
    public function issue(): void
    {
        $formatter = new NumberSequenceFormatter(
            format: $this->account->invoice_numbering_format,
            date: $this->issued_at,
        );

        $sequenceToken = $formatter->formatSequenceToken();

        /** @var \App\Models\NumberSequence $sequence */
        $sequence = $this->account->numberSequences()->firstWhere('sequence_token', $sequenceToken) ?: $this->account->numberSequences()->create([
            'sequence_token' => $sequenceToken,
            'format' => $formatter->getFormat(),
            'next_number' => 1,
        ]);

        // If the invoice number was already set by the user, we won't be touching that number.
        if (! $this->public_invoice_number) {
            $this->public_invoice_number = $formatter->formatNumber($sequence->next_number);
        }

        // If the invoice does not have variable symbol, we generate a new one.
        if (! $this->variable_symbol) {
            $variableSymbolFormatter = new NumberSequenceFormatter(
                format: $this->account->invoice_variable_symbol_format,
                date: $this->issued_at,
            );

            $this->variable_symbol = $variableSymbolFormatter->formatNumber($sequence->next_number);
        }

        // Mark the invoice as issued.
        $this->draft = false;
        $this->locked = true;
        $this->invoice_number = $sequence->next_number;
        $this->numberSequence()->associate($sequence);

        $this->save();

        // Increment a sequence, so the next invoice gets new a number.
        $sequence->incrementNextNumber();
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

    /**
     * Get the invoice currency.
     */
    public function getCurrency(): Currency
    {
        return Currency::of($this->currency);
    }
}
