<?php

namespace App\Casts;

use Brick\Money\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsMoney implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        $currencyAttribute = $this->getCurrencyAttribute();
        $currency = $model->$currencyAttribute;

        return Money::ofMinor($value, $currency);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof Money) {
            return [
                $key => $value->getMinorAmount(),
                $this->getCurrencyAttribute() => $value->getCurrency()->getCurrencyCode(),
            ];
        }

        return [
            $key => $value,
        ];
    }

    /**
     * The attribute where a currency is stored.
     */
    protected function getCurrencyAttribute(): string
    {
        return 'currency';
    }
}
