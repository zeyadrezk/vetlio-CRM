<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalDocumentItem extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalDocumentItemFactory> */
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'invoice_id',
        'name',
        'description',
        'priceable_id',
        'priceable_type',
        'service_provider_id',
        'quantity',
        'price',
        'base_price',
        'discount',
        'tax',
        'total',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'total' => MoneyCast::class,
        'base_price' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'tax' => MoneyCast::class,
    ];

    public function medicalDocument(): BelongsTo
    {
        return $this->belongsTo(MedicalDocument::class);
    }

    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }

}
