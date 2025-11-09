<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\MedicalDocumentItem;
use App\Models\Payment;
use App\Services\SequenceGenerator;
use Filament\Facades\Filament;
use Str;

class InvoiceObserver
{
    public function creating(Invoice $invoice): void
    {
        $invoice->code = SequenceGenerator::make()
            ->withModel('INVOICE')
            ->withContext([
                'branch' => Filament::getTenant()->branch_mark,
                'deviceId' => 1
            ])
            ->withPattern('{{number}}/{{branch}}/{{deviceId}}')
            ->generate()['sequence'];

        $invoice->price_list_id = Filament::getTenant()->price_list_id;
    }

    public function created(Invoice $invoice): void
    {
        $this->updateMedicalDocumentItems($invoice);
        $this->createPayment($invoice);
    }

    /**
     * @param $invoice
     * @return void
     */
    public function createPayment($invoice): void
    {
        Payment::create([
            'uuid' => Str::uuid(),
            'code' => 'PAY-' . $invoice->code,
            'branch_id' => $invoice->branch_id,
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'client_id' => $invoice->client_id,
            'organisation_id' => $invoice->organisation_id,
            'payment_method_id' => $invoice->payment_method_id,
            'transaction_id' => null,
            'note' => 'Auto payment ' . $invoice->code,
            'payment_at' => now(),
            'amount' => $invoice->total,
        ]);
    }

    private function updateMedicalDocumentItems(Invoice $invoice): void
    {
        if ($invoice->storno_of_id != null) {
            MedicalDocumentItem::where('invoice_id', $invoice->storno_of_id)->update([
                'invoice_id' => null
            ]);
        }
    }
}
