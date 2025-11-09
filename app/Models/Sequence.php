<?php

namespace App\Models;

use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use Organisationable;

    protected $fillable = [
        'organisation_id',
        'model',
        'pattern',
        'context_hash',
        'current_number',
        'year',
    ];
}
