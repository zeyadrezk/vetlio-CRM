<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['iso2','iso3','name_en','name_native','currency_id','default_language_id','phone_code'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function defaultLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'default_language_id');
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }
}
