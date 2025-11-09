<?php

namespace App\Models;

use App\Traits\AddedByCurrentUser;
use App\Traits\Organisationable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Note extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\NoteFactory> */
    use HasFactory, SoftDeletes, Organisationable, InteractsWithMedia, AddedByCurrentUser;

    protected $fillable = [
        'title',
        'user_id',
        'organisation_id',
        'note',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
