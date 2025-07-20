<?php


namespace App\Http\Controllers\Settings;


use App\Facades\Accounts;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChangeInvoiceLogoController
{
    public function __invoke(Request $request)
    {
        $account = Accounts::current();

        Gate::authorize('update', $account);

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

        if ($request->boolean('remove_file') && ($logo = $account->invoiceLogo)) {
            $account->invoiceLogo()->dissociate()->save();
            $logo->delete();
        } else if ($request->has('file') && ($file = $request->file('file'))) {
            $upload = Upload::storePublicly($file);
            $account->invoiceLogo()->associate($upload)->save();
        }

        return back();
    }
}
