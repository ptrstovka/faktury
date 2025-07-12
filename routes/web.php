<?php

use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return inertia('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/switch-account/{account}', SwitchAccountController::class)->name('accounts.switch');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
