<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    public function createInvoice($data)
    {
        $invoice = Invoice::create($data);

        $invoice->invoiceItems()->createMany($data['invoiceItems']);

        return $invoice;
    }

    public function cancelInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $storno = tap($invoice->replicate(['storno_of_id']), function ($copy) use ($invoice) {
                $copy->storno_of_id = $invoice->id;
                $copy->code = 'ST-' . $invoice->id;
                $copy->total *= -1;
                $copy->total_tax *= -1;
                $copy->total_discount *= -1;
                $copy->total_base_price *= -1;
                $copy->save();
            });

            $invoice->invoiceItems->each(function (InvoiceItem $item) use ($storno) {
                $base = $item->only([
                    'name',
                    'description',
                    'priceable_id',
                    'priceable_type',
                ]);

                $numeric = collect($item->only([
                    'total',
                    'quantity',
                    'price',
                    'base_price',
                    'discount',
                    'tax',
                ]))->map(fn($v) => $v * -1);

                $storno->invoiceItems()->create(array_merge($base, $numeric->toArray()));
            });

            Log::info('Invoice {invoice} storno created', ['invoice' => $storno->id]);;

            return $storno;
        });
    }

}
