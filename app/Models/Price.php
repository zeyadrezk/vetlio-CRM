<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    /** @use HasFactory<\Database\Factories\PriceFactory> */
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'price',
        'vat_percentage',
        'price_with_vat',
        'price_list_id',
        'valid_from_at',
    ];

    protected $casts = [
        'valid_from_at' => 'datetime',
        'price' => MoneyCast::class,
        'price_with_vat' => MoneyCast::class,
    ];

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
