<?php


namespace App\Http\Controllers\Invoice;


use App\Models\Invoice;
use App\Templating\InvoiceSerializer;
use App\Templating\SerializerOptions;
use Illuminate\Support\Facades\Gate;

class SerializeInvoiceController
{
    public function __invoke(Invoice $invoice, InvoiceSerializer $serializer)
    {
        Gate::authorize('view', $invoice);

        abort_if($invoice->draft, 400, "The draft invoice cannot be serialized");

        $options = new SerializerOptions(
            locale: $invoice->locale,
            moneyFormattingLocale: $invoice->account->getMoneyFormattingLocale(),
        );

        return response()->json($serializer->serialize($invoice, $options));
    }
}
