<?php


namespace App\Http\Controllers\Invoice;


use App\Enums\Country;
use App\Enums\PaymentMethod;
use App\Facades\Accounts;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use StackTrace\Ui\SelectOption;

class InvoiceController
{
    public function index()
    {
        return Inertia::render('Invoices/InvoiceList');
    }

    public function show(Invoice $invoice)
    {
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
            'addressCountry' => $company->address?->country_code?->value,
        ];

        return Inertia::render('Invoices/InvoiceDetail', [
            'id' => $invoice->uuid,
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
            'footer_note' => $invoice->footer_note,
            'issuedBy' => $invoice->issued_by,
            'issuedByEmail' => $invoice->issued_by_email,
            'issuedByPhone_number' => $invoice->issued_by_phone_number,
            'issuedByWebsite' => $invoice->issued_by_website,
            'paymentMethod' => $invoice->payment_method,
            'variableSymbol' => $invoice->variable_symbol,
            'specificSymbol' => $invoice->specific_symbol,
            'constantSymbol' => $invoice->constant_symbol,
            'showPayBySquare' => $invoice->show_pay_by_square,
            'vatReverseCharge' => $invoice->vat_reverse_charge,

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
        ]);
    }

    public function store()
    {
        $account = Accounts::current();

        $invoice = DB::transaction(function () use ($account) {
            $invoice = new Invoice([
                'draft' => true,
                'sent' => false,
                'paid' => false,
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

            return $invoice;
        });

        return to_route('invoices.show', $invoice);
    }

    public function update(Request $request, Invoice $invoice)
    {
        // TODO: zamknutu fakturu neni možne editovať

        $request->validate([
            'issued_at' => ['required', 'string', 'date_format:Y-m-d'],
            'supplied_at' => ['required', 'string', 'date_format:Y-m-d'],
            'payment_due_to' => ['required', 'string', 'date_format:Y-m-d'],
            'public_invoice_number' => ['nullable', 'string', 'max:191'],

            'supplier_business_name' => ['nullable', 'string', 'max:191'],
            'supplier_business_id' => ['nullable', 'string', 'max:191'],
            'supplier_vat_id' => ['nullable', 'string', 'max:191'],
            'supplier_eu_vat_id' => ['nullable', 'string', 'max:191'],
            'supplier_email' => ['nullable', 'string', 'max:191', 'email'],
            'supplier_phone_number' => ['nullable', 'string', 'max:191'],
            'supplier_website' => ['nullable', 'string', 'max:191'],
            'supplier_additional_info' => ['nullable', 'string', 'max:500'],
            'supplier_address_line_one' => ['nullable', 'string', 'max:191'],
            'supplier_address_line_two' => ['nullable', 'string', 'max:191'],
            'supplier_address_line_three' => ['nullable', 'string', 'max:191'],
            'supplier_address_city' => ['nullable', 'string', 'max:191'],
            'supplier_address_postal_code' => ['nullable', 'string', 'max:191'],
            'supplier_address_country' => ['nullable', 'string', 'max:2', Rule::enum(Country::class)],

            'customer_business_name' => ['nullable', 'string', 'max:191'],
            'customer_business_id' => ['nullable', 'string', 'max:191'],
            'customer_vat_id' => ['nullable', 'string', 'max:191'],
            'customer_eu_vat_id' => ['nullable', 'string', 'max:191'],
            'customer_email' => ['nullable', 'string', 'max:191', 'email'],
            'customer_phone_number' => ['nullable', 'string', 'max:191'],
            'customer_website' => ['nullable', 'string', 'max:191'],
            'customer_additional_info' => ['nullable', 'string', 'max:500'],
            'customer_address_line_one' => ['nullable', 'string', 'max:191'],
            'customer_address_line_two' => ['nullable', 'string', 'max:191'],
            'customer_address_line_three' => ['nullable', 'string', 'max:191'],
            'customer_address_city' => ['nullable', 'string', 'max:191'],
            'customer_address_postal_code' => ['nullable', 'string', 'max:191'],
            'customer_address_country' => ['nullable', 'string', 'max:2', Rule::enum(Country::class)],

            // TODO: pridať podporu šablony
            'template' => ['required', 'string', Rule::in(['default']), 'max:191'],
            'footer_note' => ['nullable', 'string', 'max:1000'],
            'issued_by' => ['nullable', 'string', 'max:191'],
            'issued_by_email' => ['nullable', 'string', 'max:191', 'email'],
            'issued_by_phone_number' => ['nullable', 'string', 'max:191'],
            'issued_by_website' => ['nullable', 'string', 'max:191'],

            'payment_method' => ['required', 'string', 'max:191', Rule::enum(PaymentMethod::class)],
            'bank_name' => ['nullable', 'string', 'max:191'],
            'bank_address' => ['nullable', 'string', 'max:191'],
            'bank_bic' => ['nullable', 'string', 'max:191'],
            'bank_account_number' => ['nullable', 'string', 'max:191'],
            'bank_account_iban' => ['nullable', 'string', 'max:191'],
            'variable_symbol' => ['nullable', 'string', 'max:191'],
            'specific_symbol' => ['nullable', 'string', 'max:191'],
            'constant_symbol' => ['nullable', 'string', 'max:191'],
            'show_pay_by_square' => ['boolean'],

            'vat_reverse_charge' => ['boolean'],
            'vat_enabled' => ['boolean'],

            // 'lines' => [],
        ]);

        return back();
    }
}
