<?php


namespace App\Http\Controllers\Invoice;


use App\Enums\Country;
use App\Enums\PaymentMethod;
use App\Facades\Accounts;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Support\VatBreakdownLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use StackTrace\Ui\SelectOption;

class InvoiceController
{
    public function index()
    {
        Gate::authorize('viewAny', Invoice::class);

        return Inertia::render('Invoices/InvoiceList');
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);

        $account = $invoice->account;

        $toCompany = fn (Company $company) => [
            'businessName' => $company->business_name,
            'businessId' => $company->business_id,
            'vatId' => $company->vat_id,
            'euVatId' => $company->eu_vat_id,
            'email' => $company->email,
            'phoneNumber' => $company->phone_number,
            'website' => $company->website,
            'additionalInfo' => $company->additional_info,
            'addressLineOne' => $company->address?->line_one,
            'addressLineTwo' => $company->address?->line_two,
            'addressLineThree' => $company->address?->line_three,
            'addressCity' => $company->address?->city,
            'addressPostalCode' => $company->address?->postal_code,
            'addressCountry' => $company->address?->country?->value,
        ];

        $currency = $invoice->getCurrency();

        return Inertia::render('Invoices/InvoiceDetail', [
            'id' => $invoice->uuid,
            'draft' => $invoice->draft,
            'locked' => $invoice->locked,
            'sent' => $invoice->sent,
            'publicInvoiceNumber' => $invoice->public_invoice_number,
            'supplier' => $toCompany($invoice->supplier),
            'customer' => $toCompany($invoice->customer),
            'bankName' => $invoice->supplier->bank_name,
            'bankAddress' => $invoice->supplier->bank_address,
            'bankBic' => $invoice->supplier->bank_bic,
            'bankAccountNumber' => $invoice->supplier->bank_account_number,
            'bankAccountIban' => $invoice->supplier->bank_account_iban,
            'issuedAt' => $invoice->issued_at?->format('Y-m-d'),
            'suppliedAt' => $invoice->supplied_at?->format('Y-m-d'),
            'paymentDueTo' => $invoice->payment_due_to?->format('Y-m-d'),
            'vatEnabled' => $invoice->vat_enabled,
            'locale' => $invoice->locale,
            'template' => $invoice->template,
            'footerNote' => $invoice->footer_note,
            'issuedBy' => $invoice->issued_by,
            'issuedByEmail' => $invoice->issued_by_email,
            'issuedByPhoneNumber' => $invoice->issued_by_phone_number,
            'issuedByWebsite' => $invoice->issued_by_website,
            'paymentMethod' => $invoice->payment_method,
            'variableSymbol' => $invoice->variable_symbol,
            'specificSymbol' => $invoice->specific_symbol,
            'constantSymbol' => $invoice->constant_symbol,
            'showPayBySquare' => $invoice->show_pay_by_square,
            'vatReverseCharge' => $invoice->vat_reverse_charge,
            'lines' => $invoice->getSortedLines()->map(fn (InvoiceLine $line) => [
                'title' => $line->title,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit' => $line->unit,
                'unitPrice' => $line->unit_price_vat_exclusive?->getMinorAmount(),
                'vat' => $line->vat_rate,
                'totalVatExclusive' => $line->total_price_vat_exclusive?->getMinorAmount(),
                'totalVatInclusive' => $line->total_price_vat_inclusive?->getMinorAmount(),
            ]),
            'logoUrl' => $invoice->logo?->url(),
            'signatureUrl' => $invoice->signature?->url(),
            'vatAmount' => $invoice->getVatAmount()?->getMinorAmount(),
            'vatBreakdown' => $invoice->getVatBreakdown()->map(fn (VatBreakdownLine $line) => [
                'rate' => $line->rate,
                'base' => $line->base->getMinorAmount(),
                'total' => $line->total->getMinorAmount(),
            ]),
            'totalVatInclusive' => $invoice->total_vat_inclusive?->getMinorAmount(),
            'totalVatExclusive' => $invoice->total_vat_exclusive?->getMinorAmount(),

            'countries' => Country::options(),
            'paymentMethods' => PaymentMethod::options(),
            // TODO: pridať šablóny
            'templates' => [new SelectOption('Predvolená', 'default')],

            // TODO: konfigurovateľne
            'thousandsSeparator' => '',
            // TODO: konfigurovateľne
            'decimalSeparator' => ',',
            // TODO: konfigurovateľne
            'quantityPrecision' => 4,
            // TODO: konfigurovateľne
            'pricePrecision' => 2,
            'defaultVatRate' => $account->default_vat_rate,
            'currency' => [
                'code' => $currency->getCurrencyCode(),
                'symbol' => '€', // TODO: urobiť konfigurovateľne
            ]
        ]);
    }

    public function store()
    {
        Gate::authorize('create', Invoice::class);

        $account = Accounts::current();

        $invoice = DB::transaction(function () use ($account) {
            $invoice = new Invoice([
                'draft' => true,
                'sent' => false,
                'paid' => false,
                'locked' => false,
                'payment_method' => $account->invoice_payment_method,
                // TODO: make configurable
                'currency' => 'EUR',
                'vat_enabled' => $account->vat_enabled,
                // TODO: make configurable
                'locale' => 'sk',
                'template' => $account->invoice_template,
                'footer_note' => $account->invoice_footer_note,
                'vat_reverse_charge' => false,
                'show_pay_by_square' => true,
                'issued_at' => now(),
                'supplied_at' => now(),
                'payment_due_to' => now()->addDays($account->invoice_due_days - 1),
            ]);

            $supplier = $account->company->replicate();
            $supplier->save();
            $invoice->supplier()->associate($supplier);

            $customer = new Company;
            $customer->save();
            $invoice->customer()->associate($customer);

            $invoice->account()->associate($account);
            $invoice->logo()->associate($account->invoiceLogo);
            $invoice->signature()->associate($account->invoiceSignature);

            $invoice->save();

            $invoice->lines()->create([
                'position' => 1,
                'vat_rate' => $account->vat_enabled ? $account->default_vat_rate : null,
            ]);

            return $invoice;
        });

        return to_route('invoices.show', $invoice);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        // TODO: zamknutu fakturu neni možne editovať

        DB::transaction(fn () => $request->updateInvoice($invoice));

        return back();
    }
}
