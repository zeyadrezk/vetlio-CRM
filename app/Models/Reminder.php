<?php

namespace App\Models;

use App\Filament\App\Resources\Clients\ClientResource;
use App\Filament\App\Resources\Leads\LeadResource;
use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Tickets\TicketResource;
use App\Traits\AddedByCurrentUser;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    /** @use HasFactory<\Database\Factories\ReminderFactory> */
    use HasFactory, SoftDeletes, Organisationable, AddedByCurrentUser;

    protected $fillable = [
        'title',
        'description',
        'related_id',
        'send_email',
        'related_type',
        'user_id',
        'remind_at',
        'user_to_remind_id',
        'email_sent_at'
    ];

    public function getDates()
    {
        return ['remind_at', 'email_sent_at'];
    }

    public function userToRemind(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_to_remind_id');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function isNotified(): bool
    {
        return $this->email_sent_at != null;
    }

    public function getRelatedUrl(): ?string
    {
        $related = $this->related;

        if (!$related) {
            return null;
        }

        return match (get_class($related)) {
            Client::class => ClientResource::getUrl('view', ['record' => $related]),
            Patient::class => PatientResource::getUrl('view', ['record' => $related]),
            default => null,
        };
    }
}
