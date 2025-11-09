<?php

namespace App\Models;

use App\Observers\ServiceObserver;
use App\Traits\Organisationable;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ServiceObserver::class)]
class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory, SoftDeletes, Organisationable;

    protected $fillable = [
        'name',
        'code',
        'service_group_id',
        'color',
        'active',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'datetime'
        ];
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'link_service_rooms');
    }

    public function serviceGroup(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, 'service_group_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'link_service_users');
    }

    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    public function currentPrice(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable')
            ->where('price_list_id', Filament::getTenant()->price_list_id)
            ->where('valid_from_at', '<=', now())
            ->orderBy('valid_from_at', 'desc');
    }
}
