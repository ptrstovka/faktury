<?php


namespace App\Http\Controllers\Invoice;


use App\Facades\Accounts;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController
{
    public function index()
    {
        return Inertia::render('Invoices/InvoiceList');
    }

    public function show(Invoice $invoice)
    {
        return Inertia::render('Invoices/InvoiceDetail');
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
                'show_pay_by_square' => false,
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
        return back();
    }
}
