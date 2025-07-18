<?php


namespace App\Enums;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use StackTrace\Ui\Contracts\HasLabel;
use StackTrace\Ui\SelectOption;

enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case BankTransfer = 'bank-transfer';

    /**
     * Get the payment method name.
     */
    public function name(): string
    {
        return Str::headline($this->name);
    }

    /**
     * Get the list of payment methods as select options.
     *
     * @return \Illuminate\Support\Collection<int, SelectOption>
     */
    public static function options(): Collection
    {
        return collect(PaymentMethod::cases())
            ->map(fn (PaymentMethod $method) => new SelectOption($method->name(), $method->value));
    }

    /**
     * Get the payment method name.
     */
    public function label(): string
    {
        return $this->name();
    }
}
