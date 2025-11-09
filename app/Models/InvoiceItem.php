<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceItemFactory> */
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'invoice_id',
        'name',
        'description',
        'quantity',
        'price',
        'total',
        'priceable_id',
        'priceable_type',
        'base_price',
        'discount',
        'tax',
        'deleted_at',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'total' => MoneyCast::class,
        'base_price' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'tax' => MoneyCast::class,
    ];

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }
}
