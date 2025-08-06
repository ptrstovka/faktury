<?php


namespace App\Http\Controllers;


use App\Analytics\Link;
use App\Analytics\MoneyMetric;
use App\Facades\Accounts;
use App\Models\Invoice;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController
{
    public function __invoke(Request $request)
    {
        $account = Accounts::current();
        $currency = $account->getCurrency();

        $defaultPeriod = now()->startOfYear();

        if (($requestPeriod = $request->input('period')) && is_numeric($requestPeriod) && $requestPeriod >= 2015 && $requestPeriod <= now()->year + 1) {
            $periodFrom = now()->startOfYear()->setYear((int) $requestPeriod);
            $periodUntil = $periodFrom->copy()->endOfYear();
        } else {
            $periodFrom = $defaultPeriod->copy();
            $periodUntil = $defaultPeriod->copy()->endOfYear();
        }

        $allYears = Invoice::query()
            ->toBase()
            ->where('account_id', $account->id)
            ->selectRaw("date_format(issued_at, '%Y') AS year")
            ->distinct()
            ->get()
            ->map(fn (object $result) => (int) $result->year)
            ->sortDesc()
            ->values();

        $metrics = [
            MoneyMetric::make(
                title: "Vystavené",
                value: Money::ofMinor(
                    minorAmount: $account
                        ->invoices()
                        ->where('draft', false)
                        ->whereBetween('issued_at', [$periodFrom, $periodUntil])
                        ->sum('total_vat_exclusive'),
                    currency: $currency,
                ),
                link: Link::toRoute('Zobraziť', 'invoices', query: [
                    'issued_from' => $periodFrom->format('Y-m-d'),
                    'issued_until' => $periodUntil->format('Y-m-d'),
                    'limit' => 200,
                ]),
            ),

            MoneyMetric::make(
                title: 'Neuhradené',
                value: Money::ofMinor(
                    minorAmount: $account
                        ->invoices()
                        ->where('draft', false)
                        ->where('paid', false)
                        ->whereBetween('issued_at', [$periodFrom, $periodUntil])
                        ->sum('total_vat_exclusive'),
                    currency: $currency,
                ),
                link: Link::toRoute('Zobraziť', 'invoices', query: [
                    'payment' => ['unpaid'],
                    'issued_from' => $periodFrom->format('Y-m-d'),
                    'issued_until' => $periodUntil->format('Y-m-d'),
                ]),
            ),

            MoneyMetric::make(
                title: 'Po splatnosti',
                value: Money::ofMinor(
                    minorAmount: $account
                        ->invoices()
                        ->where('draft', false)
                        ->where('paid', false)
                        ->whereBetween('issued_at', [$periodFrom, $periodUntil])
                        ->where('payment_due_to', '<', now()->startOfDay())
                        ->sum('total_vat_exclusive'),
                    currency: $currency,
                ),
                link: Link::toRoute('Zobraziť', 'invoices', query: [
                    'due' => 'true',
                    'issued_from' => $periodFrom->format('Y-m-d'),
                    'issued_until' => $periodUntil->format('Y-m-d'),
                ]),
            ),
        ];

        return Inertia::render('Dashboard', [
            'topMetrics' => $metrics,
            'year' => $periodFrom->format('Y'),
            'allYears' => $allYears,
            'defaultYear' => $periodFrom->year,
        ]);
    }
}
