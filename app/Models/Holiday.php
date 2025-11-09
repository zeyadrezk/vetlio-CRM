<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    protected $fillable = [
        'country_id','date','observed_date','fixed','global','launch_year','type','provider_uid'
    ];

    protected $casts = [
        'date' => 'date',
        'observed_date' => 'date',
        'fixed' => 'bool',
        'global' => 'bool',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(HolidayTranslation::class);
    }
}
