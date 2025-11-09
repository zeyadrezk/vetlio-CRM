<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationReminderDelivery extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationReminderDeliveryFactory> */
    use HasFactory;

    protected $fillable = [
        'reservation_reminder_id',
        'channel',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function reservationReminder(): BelongsTo
    {
        return $this->belongsTo(ReservationReminder::class);
    }
}
