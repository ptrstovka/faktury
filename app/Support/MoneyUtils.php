<?php


namespace App\Support;


use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;
use RuntimeException;

final readonly class MoneyUtils
{
    /**
     * Calculate total sum of money.
     */
    public static function sum(string $currency, Money ...$monies): Money
    {
        try {
            return Money::total(Money::zero($currency), ...$monies);
        } catch (MoneyMismatchException $e) {
            throw new RuntimeException($e->getMessage(), previous: $e);
        }
    }
}
