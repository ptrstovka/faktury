<?php

use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return inertia('Dashboard');
    })->name('dashboard');

    Route::post('/switch-account/{account}', SwitchAccountController::class)->name('accounts.switch');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice:uuid}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('/invoices/{invoice:uuid}', [InvoiceController::class, 'update'])->name('invoices.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
