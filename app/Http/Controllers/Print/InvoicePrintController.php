<?php

namespace App\Http\Controllers\Print;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicePrintController extends BasePrintController
{
    protected function view(): string
    {
        return 'pdf.invoice';
    }

    public function inline( $record)
    {
        $invoice = Invoice::findOrFail($record);

        return parent::inline($invoice);
    }

    public function download($record)
    {
        $invoice = Invoice::findOrFail($record);

        return parent::download($invoice);
    }
}
