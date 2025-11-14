<?php

use App\Http\Controllers\Print\InvoicePrintController;
use App\Http\Controllers\TenantSelectorController;
use Illuminate\Support\Facades\Route;

// Tenant selector routes (Development only - for local tenant switching)
if (app()->environment('local')) {
    Route::get('/select-tenant', [TenantSelectorController::class, 'index'])->name('select-tenant');
    Route::post('/select-tenant', [TenantSelectorController::class, 'select'])->name('select-tenant.select');
    Route::post('/select-tenant/clear', [TenantSelectorController::class, 'clear'])->name('select-tenant.clear');
}

Route::prefix('print')->name('print.')->group(function () {
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('{record}/inline', [InvoicePrintController::class, 'inline'])->name('inline');
        Route::get('{record}/download', [InvoicePrintController::class, 'download'])->name('download');
    });
});
