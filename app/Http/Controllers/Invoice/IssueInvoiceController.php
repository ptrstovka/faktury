<?php


namespace App\Http\Controllers\Invoice;


use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class IssueInvoiceController
{
    public function __invoke(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        DB::transaction(fn () => $invoice->issue());

        return back();
    }
}
