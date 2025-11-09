<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceFiscalisation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'zki',
        'jir',
        'qrcode',
        'request_xml',
        'response_xml',
        'status',
        'error_message',
    ];
}
