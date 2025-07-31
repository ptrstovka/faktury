<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Invoice\DownloadInvoiceController;
use App\Http\Controllers\Invoice\DuplicateController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Invoice\InvoiceLockController;
use App\Http\Controllers\Invoice\IssueInvoiceController;
use App\Http\Controllers\Invoice\PaidFlagController;
use App\Http\Controllers\Invoice\SendController;
use App\Http\Controllers\Invoice\SentFlagController;
use App\Http\Controllers\Invoice\SerializeInvoiceController;
use App\Http\Controllers\SwitchAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::post('/switch-account/{account}', SwitchAccountController::class)->name('accounts.switch');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice:uuid}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('/invoices/{invoice:uuid}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice:uuid}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice:uuid}/json', SerializeInvoiceController::class)->name('invoices.serialize');
    Route::post('/invoices/{invoice:uuid}/issue', IssueInvoiceController::class)->name('invoices.issue');
    Route::post('/invoices/{invoice:uuid}/lock', [InvoiceLockController::class, 'store'])->name('invoices.lock.store');
    Route::delete('/invoices/{invoice:uuid}/lock', [InvoiceLockController::class, 'destroy'])->name('invoices.lock.destroy');
    Route::get('/invoices/{invoice:uuid}/download', DownloadInvoiceController::class)->name('invoices.download');
    Route::post('/invoices/{invoice:uuid}/flags/sent', [SentFlagController::class, 'store'])->name('invoices.sent-flag.store');
    Route::delete('/invoices/{invoice:uuid}/flags/sent', [SentFlagController::class, 'destroy'])->name('invoices.sent-flag.destroy');
    Route::post('/invoices/{invoice:uuid}/flags/paid', [PaidFlagController::class, 'store'])->name('invoices.paid-flag.store');
    Route::delete('/invoices/{invoice:uuid}/flags/paid', [PaidFlagController::class, 'destroy'])->name('invoices.paid-flag.destroy');
    Route::post('/invoices/{invoice:uuid}/send', SendController::class)->name('invoices.send')->middleware('throttle:mail');
    Route::post('/invoices/{invoice:uuid}/duplicate', DuplicateController::class)->name('invoices.duplicate');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
