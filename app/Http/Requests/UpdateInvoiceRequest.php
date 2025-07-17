<?php

namespace App\Http\Requests;

use App\Enums\Country;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Invoice;
use Brick\Money\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invoice = $this->invoice();

        if (! Gate::allows('update', $invoice)) {
            return false;
        }

        abort_if($invoice->locked, 400, "Locked invoice cannot be modified");

        return true;
    }

    /**
     * Get the invoice which is being edited.
     */
    public function invoice(): Invoice
    {
        return $this->route('invoice');
    }

    public function rules(): array
    {
        $issued = ! $this->invoice()->draft;

        return [
            'issued_at' => ['required', 'string', 'date_format:Y-m-d'],
            'supplied_at' => ['required', 'string', 'date_format:Y-m-d'],
            'payment_due_to' => ['required', 'string', 'date_format:Y-m-d'],
            'public_invoice_number' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],

            'supplier_business_name' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'supplier_business_id' => ['nullable', 'string', 'max:191'],
            'supplier_vat_id' => ['nullable', 'string', 'max:191'],
            'supplier_eu_vat_id' => ['nullable', 'string', 'max:191'],
            'supplier_email' => ['nullable', 'string', 'max:191', 'email'],
            'supplier_phone_number' => ['nullable', 'string', 'max:191'],
            'supplier_website' => ['nullable', 'string', 'max:191'],
            'supplier_additional_info' => ['nullable', 'string', 'max:500'],
            'supplier_address_line_one' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'supplier_address_line_two' => ['nullable', 'string', 'max:191'],
            'supplier_address_line_three' => ['nullable', 'string', 'max:191'],
            'supplier_address_city' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'supplier_address_postal_code' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'supplier_address_country' => [$issued ? 'required' : 'nullable', 'string', 'max:2', Rule::enum(Country::class)],

            'customer_business_name' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'customer_business_id' => ['nullable', 'string', 'max:191'],
            'customer_vat_id' => ['nullable', 'string', 'max:191'],
            'customer_eu_vat_id' => ['nullable', 'string', 'max:191'],
            'customer_email' => ['nullable', 'string', 'max:191', 'email'],
            'customer_phone_number' => ['nullable', 'string', 'max:191'],
            'customer_website' => ['nullable', 'string', 'max:191'],
            'customer_additional_info' => ['nullable', 'string', 'max:500'],
            'customer_address_line_one' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'customer_address_line_two' => ['nullable', 'string', 'max:191'],
            'customer_address_line_three' => ['nullable', 'string', 'max:191'],
            'customer_address_city' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'customer_address_postal_code' => [$issued ? 'required' : 'nullable', 'string', 'max:191'],
            'customer_address_country' => [$issued ? 'required' : 'nullable', 'string', 'max:2', Rule::enum(Country::class)],

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

            'lines' => ['array', 'max:100', $issued ? 'min:1' : 'min:0'],
            'lines.*.title' => ['nullable', 'string', 'max:500'],
            'lines.*.description' => ['nullable', 'string', 'max:1000'],
            'lines.*.quantity' => ['nullable', 'numeric'],
            'lines.*.unit' => ['nullable', 'string', 'max:191'],
            'lines.*.unitPrice' => ['nullable', 'integer'],
            'lines.*.vat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.totalVatExclusive' => ['nullable', 'integer'],
            'lines.*.totalVatInclusive' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'lines.*.title' => 'title',
            'lines.*.description' => 'description',
            'lines.*.quantity' => 'quantity',
            'lines.*.unit' => 'unit',
            'lines.*.unitPrice' => 'unitPrice',
            'lines.*.vat' => 'vat',
            'lines.*.totalVatExclusive' => 'totalVatExclusive',
            'lines.*.totalVatInclusive' => 'totalVatInclusive',
        ];
    }

    public function fulfill(Invoice $invoice): void
    {
        $invoice->fill([
            'issued_at' => $this->date('issued_at', 'Y-m-d'),
            'supplied_at' => $this->date('supplied_at', 'Y-m-d'),
            'payment_due_to' => $this->date('payment_due_to', 'Y-m-d'),
            'public_invoice_number' => $this->input('public_invoice_number'),
            'template' => $this->input('template'),
            'footer_note' => $this->input('footer_note'),
            'issued_by' => $this->input('issued_by'),
            'issued_by_email' => $this->input('issued_by_email'),
            'issued_by_phone_number' => $this->input('issued_by_phone_number'),
            'issued_by_website' => $this->input('issued_by_website'),
            'vat_reverse_charge' => $this->boolean('vat_reverse_charge'),
            'vat_enabled' => $this->boolean('vat_enabled'),
            'payment_method' => $this->enum('payment_method', PaymentMethod::class),
            'variable_symbol' => $this->input('variable_symbol'),
            'specific_symbol' => $this->input('specific_symbol'),
            'constant_symbol' => $this->input('constant_symbol'),
            'show_pay_by_square' => $this->boolean('show_pay_by_square'),
        ]);
        $invoice->save();

        $supplierAddress = $invoice->supplier->address ?: new Address;
        $supplierAddress->fill([
            'line_one' => $this->input('supplier_address_line_one'),
            'line_two' => $this->input('supplier_address_line_two'),
            'line_three' => $this->input('supplier_address_line_three'),
            'city' => $this->input('supplier_address_city'),
            'postal_code' => $this->input('supplier_address_postal_code'),
            'country' => $this->enum('supplier_address_country', Country::class),
        ]);
        $supplierAddress->save();
        $invoice->supplier->address()->associate($supplierAddress);
        $invoice->supplier->fill([
            'business_name' => $this->input('supplier_business_name'),
            'business_id' => $this->input('supplier_business_id'),
            'vat_id' => $this->input('supplier_vat_id'),
            'eu_vat_id' => $this->input('supplier_eu_vat_id'),
            'email' => $this->input('supplier_email'),
            'phone_number' => $this->input('supplier_phone_number'),
            'website' => $this->input('supplier_website'),
            'additional_info' => $this->input('supplier_additional_info'),
            'bank_name' => $this->input('bank_name'),
            'bank_address' => $this->input('bank_address'),
            'bank_bic' => $this->input('bank_bic'),
            'bank_account_number' => $this->input('bank_account_number'),
            'bank_account_iban' => $this->input('bank_account_iban'),
        ]);
        $invoice->supplier->save();

        $customerAddress = $invoice->customer->address ?: new Address;
        $customerAddress->fill([
            'line_one' => $this->input('customer_address_line_one'),
            'line_two' => $this->input('customer_address_line_two'),
            'line_three' => $this->input('customer_address_line_three'),
            'city' => $this->input('customer_address_city'),
            'postal_code' => $this->input('customer_address_postal_code'),
            'country' => $this->enum('customer_address_country', Country::class),
        ]);
        $customerAddress->save();
        $invoice->customer->address()->associate($customerAddress);
        $invoice->customer->fill([
            'business_name' => $this->input('customer_business_name'),
            'business_id' => $this->input('customer_business_id'),
            'vat_id' => $this->input('customer_vat_id'),
            'eu_vat_id' => $this->input('customer_eu_vat_id'),
            'email' => $this->input('customer_email'),
            'phone_number' => $this->input('customer_phone_number'),
            'website' => $this->input('customer_website'),
            'additional_info' => $this->input('customer_additional_info'),
        ]);
        $invoice->customer->save();

        $invoice->lines()->delete();
        $this->collect('lines')->each(function (array $line, int $idx) use ($invoice) {
            $unitPrice = Arr::get($line, 'unitPrice');
            $totalVatInclusive = Arr::get($line, 'totalVatInclusive');
            $totalVatExclusive = Arr::get($line, 'totalVatExclusive');

            $invoice->lines()->create([
                'position' => $idx + 1,
                'title' => Arr::get($line, 'title'),
                'description' => Arr::get($line, 'description'),
                'unit' => Arr::get($line, 'unit'),
                'quantity' => Arr::get($line, 'quantity'),
                'vat_rate' => Arr::get($line, 'vat'),
                'unit_price_vat_exclusive' => is_numeric($unitPrice) ? Money::ofMinor($unitPrice, $invoice->currency) : null,
                'total_price_vat_inclusive' => is_numeric($totalVatInclusive) ? Money::ofMinor($totalVatInclusive, $invoice->currency) : null,
                'total_price_vat_exclusive' => is_numeric($totalVatExclusive) ? Money::ofMinor($totalVatExclusive, $invoice->currency) : null,
                'currency' => $invoice->currency,
            ]);
        });
    }
}
