<?php

namespace App\Models;

use App\Contracts\TaskRelated;
use App\Traits\Organisationable;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Tags\HasTags;
use Swindon\FilamentHashids\Traits\HasHashid;

class Client extends Authenticatable implements HasName, HasAvatar, MustVerifyEmail, TaskRelated
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory, SoftDeletes, Notifiable, Organisationable, HasTags, HasHashid;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'website',
        'email',
        'address',
        'city',
        'avatar_url',
        'active',
        'zip_code',
        'country_id',
        'gender_id',
        'date_of_birth',
        'oib'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function fullName(): Attribute
    {
        return Attribute::make(function () {
            return $this->first_name . ' ' . $this->last_name;
        });
    }

    public function fullAddress(): Attribute
    {
        return Attribute::make(function () {
            return implode(', ', [$this->address, $this->city, $this->zip_code]);
        });
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function itemsToPay(): HasManyThrough|Client
    {
        return $this->hasManyThrough(
            MedicalDocumentItem::class,
            MedicalDocument::class,
            'client_id',
            'medical_document_id',
            'id',
            'id'
        )->whereNull('invoice_id');
    }

    public function medicalDocuments(): HasManyThrough
    {
        return $this->hasManyThrough(
            MedicalDocument::class,
            Patient::class,
            'client_id',
            'patient_id',
            'id',
            'id'
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'related')->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function reminders(): MorphMany
    {
        return $this->morphMany(Reminder::class, 'related')->latest();
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'related')->latest();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'related')->latest();
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    public function lastReservation(): HasOne|Client
    {
        return $this->hasOne(Reservation::class, 'client_id')
            ->whereBeforeToday('date')
            ->latest();
    }

    public function nextReservation(): HasOne|Client
    {
        return $this->hasOne(Reservation::class, 'client_id')
            ->whereAfterToday('date')
            ->latest();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'client_id');
    }

    public function dismissedAnnouncements(): MorphToMany
    {
        return $this->morphToMany(Announcement::class, 'dismissable', 'dismissed_announcements')
            ->withPivot(['read_at'])
            ->withTimestamps();
    }

    public function unreadAnnouncements(): Builder
    {
        return Announcement::query()
            ->forClients()
            ->active()
            ->whereDoesntHave('dismissedByClients', function ($q) {
                $q->where('dismissable_id', $this->id)
                    ->where('dismissable_type', static::class);
            })
            ->orderBy('starts_at');
    }

    public function nextUnreadAnnouncement()
    {
        return $this->unreadAnnouncements()->first();
    }

    public function markAnnouncementAsRead(Announcement $announcement): void
    {
        if (!$announcement->for_clients) {
            return;
        }

        $this->dismissedAnnouncements()->syncWithoutDetaching([
            $announcement->getKey() => ['read_at' => now()],
        ]);
    }

    public function relatedValue()
    {
        return $this->full_name;
    }

    public function relatedLabel()
    {
        return 'Klijent';
    }
}
