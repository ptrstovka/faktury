<?php


namespace App\Http\Controllers\Settings;


use Inertia\Inertia;

class AccountController
{
    public function edit()
    {
        return Inertia::render('settings/Account');
    }

    public function update()
    {
        return back();
    }
}
