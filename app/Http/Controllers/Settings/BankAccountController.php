<?php


namespace App\Http\Controllers\Settings;


use App\Facades\Accounts;
use Illuminate\Http\Request;

class BankAccountController
{
    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => ['nullable', 'string', 'max:191'],
            'bank_address' => ['nullable', 'string', 'max:191'],
            'bank_bic' => ['nullable', 'string', 'max:191'],
            'bank_account_number' => ['nullable', 'string', 'max:191'],
            'bank_account_iban' => ['nullable', 'string', 'max:191'],
        ]);

        $account = Accounts::current();

        $account->company->update([
            'bank_name' => $request->input('bank_name'),
            'bank_address' => $request->input('bank_address'),
            'bank_bic' => $request->input('bank_bic'),
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_account_iban' => $request->input('bank_account_iban'),
        ]);

        return back();
    }
}
