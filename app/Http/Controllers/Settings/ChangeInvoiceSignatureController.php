<?php


namespace App\Http\Controllers\Settings;


use App\Facades\Accounts;
use App\Models\Upload;
use Illuminate\Http\Request;

class ChangeInvoiceSignatureController
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => [
                'nullable',
                'image',
                'max:8192',
                'dimensions:min_width=100,min_height=100,max_width=400,max_height=400',
                'extensions:jpg,png,jpeg',
                'mimes:jpg,png,jpeg',
            ],
            'remove_file' => ['boolean'],
        ]);

        $account = Accounts::current();

        if ($request->boolean('remove_file') && ($signature = $account->invoiceSignature)) {
            $account->invoiceSignature()->dissociate()->save();
            $signature->delete();
        } else if ($request->has('file') && ($file = $request->file('file'))) {
            $upload = Upload::storePublicly($file);
            $account->invoiceSignature()->associate($upload)->save();
        }

        return back();
    }
}
