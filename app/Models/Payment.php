<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\PaymentMethod;
use App\Observers\PaymentObserver;
use App\Traits\AddedByCurrentUser;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([PaymentObserver::class])]
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory, SoftDeletes, Organisationable, AddedByCurrentUser;

    protected $fillable = [
        'uuid',
        'code',
        'branch_id',
        'invoice_id',
        'user_id',
        'payment_method_id',
        'transaction_id',
        'client_id',
        'note',
        'payment_at',
        'amount',
        'organisation_id',
    ];

    protected $casts = [
        'payment_at' => 'datetime',
        'amount' => MoneyCast::class,
        'payment_method_id' => PaymentMethod::class,
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($offer) {
            $offer->uuid = (string)\Str::uuid();
            $offer->code = 1;
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
