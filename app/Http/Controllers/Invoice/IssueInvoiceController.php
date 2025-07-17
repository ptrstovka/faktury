<?php


namespace App\Http\Controllers\Invoice;


use App\Http\Requests\IssueInvoiceRequest;
use App\Models\Invoice;
use App\NumberSequenceFormatter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IssueInvoiceController
{
    public function __invoke(IssueInvoiceRequest $request, Invoice $invoice)
    {
        /** @var Invoice $invoice */
        $invoice = DB::transaction(fn () => $request->saveInvoice($invoice));

        Cache::lock("IssueInvoice:".$invoice->account->id, 10)->block(5, function () use ($invoice) {
            $formatter = new NumberSequenceFormatter(
                format: $invoice->account->invoice_numbering_format,
                date: $invoice->issued_at,
            );

            $sequenceToken = $formatter->formatSequenceToken();

            /** @var \App\Models\NumberSequence $sequence */
            $sequence = $invoice->account->numberSequences()->firstWhere('sequence_token', $sequenceToken) ?: $invoice->account->numberSequences()->create([
                'sequence_token' => $sequenceToken,
                'format' => $formatter->getFormat(),
                'next_number' => 1,
            ]);

            // If the invoice number was already set by the user, we won't be touching that number.
            if (! $invoice->public_invoice_number) {
                $invoice->public_invoice_number = $formatter->formatNumber($sequence->next_number);
            }

            // If the invoice does not have variable symbol, we generate a new one.
            if (! $invoice->variable_symbol) {
                $variableSymbolFormatter = new NumberSequenceFormatter(
                    format: $invoice->account->invoice_variable_symbol_format,
                    date: $invoice->issued_at,
                );

                $invoice->variable_symbol = $variableSymbolFormatter->formatNumber($sequence->next_number);
            }

            // The sequence needs to be always incremented.
            $sequence->incrementNextNumber();

            // Mark the invoice as issued.
            $invoice->draft = false;
            $invoice->locked = true;

            $invoice->save();
        });

        return back();
    }
}
