<?php

use App\Http\Controllers\Print\InvoicePrintController;
use Illuminate\Support\Facades\Route;

Route::prefix('print')->name('print.')->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('{record}/inline', [InvoicePrintController::class, 'inline'])->name('inline');
        Route::get('{record}/download', [InvoicePrintController::class, 'download'])->name('download');
    });
});
