<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = ['iso_639_1', 'iso_639_2', 'name_en', 'name_native'];

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class, 'default_language_id');
    }
}
