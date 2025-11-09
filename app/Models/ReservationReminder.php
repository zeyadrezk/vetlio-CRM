<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ReservationReminder extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationReminderFactory> */
    use HasFactory;

    protected $fillable = [
        'offset_amount',
        'offset_unit',
        'channels',
        'scheduled_at',
        'reservation_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'channels' => 'array',
        'offset_amount' => 'integer',
        'offset_unit' => 'string',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::created(function (ReservationReminder $reminder) {
            $reminder->createDeliveries($reminder);
        });

        static::deleting(function (ReservationReminder $reminder) {
            $reminder->deleteDeliveries($reminder);
        });
    }


    public function deliveries(): HasMany
    {
        return $this->hasMany(ReservationReminderDelivery::class, 'reservation_reminder_id');
    }

    function createDeliveries(ReservationReminder $reminder): void
    {
        foreach ($reminder->channels as $channel) {
            $reminder->deliveries()->create([
                'channel' => $channel,
                'status' => 'pending',
            ]);
        }
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    function deleteDeliveries(ReservationReminder $reminder): void
    {
        $reminder->deliveries()->each(function ($delivery) {
            $delivery->forceDelete();
        });
    }

}
