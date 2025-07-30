<?php


namespace App\Http\Controllers\Invoice;


use App\Models\Invoice;
use Illuminate\Support\Facades\Gate;

class PaidFlagController
{
    public function store(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        abort_if($invoice->draft, 400, "Draft invoices cannot be modified");

        $invoice->paid = true;
        $invoice->save();

        return back();
    }

    public function destroy(Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        abort_if($invoice->draft, 400, "Draft invoices cannot be modified");

        $invoice->paid = false;
        $invoice->save();

        return back();
    }
}
