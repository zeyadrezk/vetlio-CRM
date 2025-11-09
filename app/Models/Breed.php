<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Breed extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'species_id',
        'organization_id',
        'name',
        'is_custom',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

}
