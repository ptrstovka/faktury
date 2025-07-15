<?php


namespace App\Http\Controllers\Settings;


use App\Enums\PaymentMethod;
use App\Facades\Accounts;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use StackTrace\Ui\SelectOption;

class InvoiceController
{
    public function edit()
    {
        $account = Accounts::current();

        return Inertia::render('Settings/Invoice', [
            'vatEnabled' => $account->vat_enabled,
            'numberingFormat' => $account->invoice_numbering_format,
            'defaultVatRate' => $account->default_vat_rate,
            'dueDays' => $account->invoice_due_days,
            'footerNote' => $account->invoice_footer_note,
            'template' => $account->invoice_template,
            // TODO: pridať šablóny
            'templates' => [new SelectOption('Predvolená', 'default')],
            'paymentMethod' => $account->invoice_payment_method->value,
            'paymentMethods' => PaymentMethod::options(),
            'signatureFileUrl' => $account->invoiceSignature?->url(),
            'logoFileUrl' => $account->invoiceLogo?->url(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'numbering_format' => ['required', 'string', 'max:191'],
            'default_vat_rate' => ['required', 'numeric', 'max:99', 'min:0'],
            'due_days' => ['required', 'numeric', 'max:365', 'min:0'],
            'footer_note' => ['nullable', 'string', 'max:500'],
            // TODO: pridať šablóny
            'template' => ['required', 'string', Rule::in(['default'])],
            'payment_method' => ['required', 'string', Rule::enum(PaymentMethod::class)],
        ]);

        $account = Accounts::current();

        $account->update([
            'invoice_numbering_format' => $request->input('numbering_format'),
            'default_vat_rate' => $request->input('default_vat_rate'),
            'invoice_due_days' => $request->input('due_days'),
            'invoice_footer_note' => $request->input('footer_note'),
            'invoice_template' => $request->input('template'),
            'invoice_payment_method' => $request->enum('payment_method', PaymentMethod::class),
        ]);

        return back();
    }
}
