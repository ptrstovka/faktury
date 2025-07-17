<?php


namespace App\Http\Controllers\Invoice;


use App\Enums\Country;
use App\Enums\PaymentMethod;
use App\Facades\Accounts;
use App\Models\Address;
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
            'addressCountry' => $company->address?->country?->value,
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

        $invoice->fill([
            'issued_at' => $request->date('issued_at', 'Y-m-d'),
            'supplied_at' => $request->date('supplied_at', 'Y-m-d'),
            'payment_due_to' => $request->date('payment_due_to', 'Y-m-d'),
            'public_invoice_number' => $request->input('public_invoice_number'),
            'template' => $request->input('template'),
            'footer_note' => $request->input('footer_note'),
            'issued_by' => $request->input('issued_by'),
            'issued_by_email' => $request->input('issued_by_email'),
            'issued_by_phone_number' => $request->input('issued_by_phone_number'),
            'issued_by_website' => $request->input('issued_by_website'),
            'vat_reverse_charge' => $request->boolean('vat_reverse_charge'),
            'vat_enabled' => $request->boolean('vat_enabled'),
            'payment_method' => $request->enum('payment_method', PaymentMethod::class),
            'variable_symbol' => $request->input('variable_symbol'),
            'specific_symbol' => $request->input('specific_symbol'),
            'constant_symbol' => $request->input('constant_symbol'),
            'show_pay_by_square' => $request->boolean('show_pay_by_square'),
        ]);
        $invoice->save();

        $supplierAddress = $invoice->supplier->address ?: new Address;
        $supplierAddress->fill([
            'line_one' => $request->input('supplier_address_line_one'),
            'line_two' => $request->input('supplier_address_line_two'),
            'line_three' => $request->input('supplier_address_line_three'),
            'city' => $request->input('supplier_address_city'),
            'postal_code' => $request->input('supplier_address_postal_code'),
            'country' => $request->enum('supplier_address_country', Country::class),
        ]);
        $supplierAddress->save();
        $invoice->supplier->address()->associate($supplierAddress);
        $invoice->supplier->fill([
            'business_name' => $request->input('supplier_business_name'),
            'business_id' => $request->input('supplier_business_id'),
            'vat_id' => $request->input('supplier_vat_id'),
            'eu_vat_id' => $request->input('supplier_eu_vat_id'),
            'email' => $request->input('supplier_email'),
            'phone_number' => $request->input('supplier_phone_number'),
            'website' => $request->input('supplier_website'),
            'additional_info' => $request->input('supplier_additional_info'),
            'bank_name' => $request->input('bank_name'),
            'bank_address' => $request->input('bank_address'),
            'bank_bic' => $request->input('bank_bic'),
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_account_iban' => $request->input('bank_account_iban'),
        ]);
        $invoice->supplier->save();

        $customerAddress = $invoice->customer->address ?: new Address;
        $customerAddress->fill([
            'line_one' => $request->input('customer_address_line_one'),
            'line_two' => $request->input('customer_address_line_two'),
            'line_three' => $request->input('customer_address_line_three'),
            'city' => $request->input('customer_address_city'),
            'postal_code' => $request->input('customer_address_postal_code'),
            'country' => $request->enum('customer_address_country', Country::class),
        ]);
        $customerAddress->save();
        $invoice->customer->address()->associate($customerAddress);
        $invoice->customer->fill([
            'business_name' => $request->input('customer_business_name'),
            'business_id' => $request->input('customer_business_id'),
            'vat_id' => $request->input('customer_vat_id'),
            'eu_vat_id' => $request->input('customer_eu_vat_id'),
            'email' => $request->input('customer_email'),
            'phone_number' => $request->input('customer_phone_number'),
            'website' => $request->input('customer_website'),
            'additional_info' => $request->input('customer_additional_info'),
        ]);
        $invoice->customer->save();

        return back();
    }
}
